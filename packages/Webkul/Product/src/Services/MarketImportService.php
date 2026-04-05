<?php

namespace Webkul\Product\Services;

use Illuminate\Support\Facades\Storage;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Attribute\Models\AttributeFamily;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Bus;
use Webkul\Product\Helpers\Indexers\Flat as FlatIndexer;
use Webkul\Product\Jobs\UpdateCreateInventoryIndexJob;
use Webkul\Product\Jobs\UpdateCreatePriceIndexJob;
use Webkul\Product\Jobs\ElasticSearch\UpdateCreateIndex as UpdateCreateElasticSearchIndexJob;

class MarketImportService
{
    /**
     * Mapping for Yandex Market Parameter IDs to Bagisto Attribute Codes
     */
    protected $attributeMapping = [
        37821410 => 'nominal',
        37919770 => 'activation_period',
        37978250 => 'purpose',
        37693330 => 'type_of_product',
        37919810 => 'region',
    ];

    public function __construct(
        protected ProductRepository $productRepository,
        protected CategoryRepository $categoryRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeOptionRepository $attributeOptionRepository,
        protected ProductAttributeValueRepository $productAttributeValueRepository,
        protected FlatIndexer $flatIndexer
    ) {}

    /**
     * Import offer from Yandex Market style JSON
     */
    public function import(array $data)
    {
        $offer = $data['offer'];
        $sku = $offer['offerId'];

        // 1. Ensure Category exists for codes
        $category = $this->ensureCategory($offer['marketCategoryId']);

        // 2. Determine Attribute Family (Default or Meanly Digital if exists)
        $attributeFamily = AttributeFamily::where('code', 'meanly_digital')->first() ?: AttributeFamily::where('code', 'default')->first();

        // 3. Check if product exists
        $product = $this->productRepository->findOneByField('sku', $sku);

        if (!$product) {
            $productData = [
                'type'                => 'downloadable',
                'attribute_family_id' => $attributeFamily->id,
                'sku'                 => $sku,
            ];

            $product = $this->productRepository->create($productData);
        }

        // 4. Update Product Details
        $updateData = [
            'name'           => $offer['name'],
            'description'    => $offer['description'],
            'short_description' => $offer['name'],
            'url_key'        => Str::slug($offer['name']) . '-' . $sku,
            'price'          => $offer['basicPrice']['value'],
            'status'         => 1,
            'visible_individually' => 1,
            'categories'     => [$category->id],
            'channels'       => [1], // Default channel
        ];

        // 4.1 Populate data for all locales to ensure visibility in any admin language
        $locales = core()->getAllLocales()->pluck('code');
        foreach ($locales as $locale) {
            $updateData[$locale] = [
                'name'              => $offer['name'],
                'description'       => $offer['description'],
                'short_description' => $offer['name'],
                'url_key'           => Str::slug($offer['name']) . '-' . $sku . '-' . $locale,
                'short_name'        => $this->generateShortName($offer['name']),
            ];
        }

        // 5. Handle Brand (vendor)
        if (isset($offer['vendor'])) {
            $updateData['brand'] = $this->getOrCreateOptionId('brand', $offer['vendor']);
        }

        // 5.1 Generate Short Name for Storefront
        if (isset($offer['name'])) {
            $updateData['short_name'] = $this->generateShortName($offer['name']);
        }

        // 6. Handle Parameters
        foreach ($offer['parameterValues'] as $param) {
            if (isset($this->attributeMapping[$param['parameterId']])) {
                $attrCode = $this->attributeMapping[$param['parameterId']];
                $updateData[$attrCode] = $param['value'];
            }
        }

        // 7. Perform standard update
        $product = $this->productRepository->update($updateData, $product->id);

        // 8. Ensure flat table schema is in sync (adds missing columns)
        $this->flatIndexer->createTable();

        // 8.1 Refresh flat index for visibility in Admin
        $this->flatIndexer->refresh($product);

        // 8.2 Dispatch heavy indexing jobs (Price, Inventory, ElasticSearch)
        $productIds = [$product->id];
        Bus::chain([
            new UpdateCreateInventoryIndexJob($productIds),
            new UpdateCreatePriceIndexJob($productIds),
            new UpdateCreateElasticSearchIndexJob($productIds),
        ])->dispatch();

        // 9. Handle Images (Placeholder logic for now)
        // $this->handleImages($product, $offer['pictures']);

        // 10. Diagnostic Info
        $productFlat = \DB::table('product_flat')->where('product_id', $product->id)->get();
        $hasFlatColumn = \Schema::hasColumn('product_flat', 'short_name');

        return [
            'product'    => $product,
            'diagnostic' => [
                'attribute_exists'   => (bool) $this->attributeRepository->findOneByField('code', 'short_name'),
                'flat_column_exists' => $hasFlatColumn,
                'indexed_locales'    => $productFlat->pluck('locale')->toArray(),
                'flat_data_sample'   => $productFlat->map(fn($f) => [
                    'locale'     => $f->locale,
                    'short_name' => $hasFlatColumn ? ($f->short_name ?? 'NULL') : 'COLUMN_MISSING'
                ])
            ]
        ];
    }

    /**
     * Generate a short user-friendly name
     */
    protected function generateShortName($name)
    {
        $name = preg_replace('/\s*[\(\[].*?[\)\]]/u', '', $name);
        $patterns = [
            '/подарочная карта/ui',
            '/gift card/ui',
            '/цифровой код/ui',
            '/для для/ui',
            '/для/ui',
            '/подписка/ui',
            '/карта оплаты/ui',
            '/сертификат/ui',
            '/электронный ключ/ui',
        ];
        $name = preg_replace($patterns, '', $name);
        $name = preg_replace('/\s+(US|США|RU|UK|TR|PL)$/ui', '', $name);
        $name = trim(preg_replace('/\s+/', ' ', $name));
        return $name ?: 'Product';
    }

    /**
     * Find or create category by market code
     */
    protected function ensureCategory($marketCategoryId)
    {
        $categoryName = "Коды пополнения";
        $slug = Str::slug($categoryName);
        $category = $this->categoryRepository->findBySlug($slug);
        if (!$category) {
            $localeCode = core()->getCurrentLocale()->code;
            $data = [
                'status'       => 1,
                'position'     => 1,
                'display_mode' => 'products_and_description',
                $localeCode    => [
                    'name'             => $categoryName,
                    'slug'             => $slug,
                    'description'      => $categoryName,
                    'meta_title'       => $categoryName,
                    'meta_description' => $categoryName,
                    'meta_keywords'    => $categoryName,
                ],
            ];
            $category = $this->categoryRepository->create($data);
        }
        return $category;
    }

    /**
     * Get or create attribute option
     */
    protected function getOrCreateOptionId($attributeCode, $value)
    {
        $attribute = $this->attributeRepository->findOneByField('code', $attributeCode);
        if (!$attribute) return null;
        $option = $this->attributeOptionRepository->findWhere([
            'attribute_id' => $attribute->id,
            'admin_name'   => $value
        ])->first();
        if (!$option) {
            $option = $this->attributeOptionRepository->create([
                'attribute_id' => $attribute->id,
                'admin_name'   => $value
            ]);
        }
        return $option->id;
    }
}
