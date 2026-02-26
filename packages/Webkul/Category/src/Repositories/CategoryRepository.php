<?php

namespace Webkul\Category\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Webkul\Category\Contracts\Category;
use Webkul\Category\Models\CategoryTranslationProxy;
use Webkul\Core\Eloquent\Repository;

class CategoryRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Category::class;
    }

    /**
     * Get categories.
     *
     * @return void
     */
    public function getAll(array $params = [])
    {
        $queryBuilder = $this->query()
            ->select('categories.*')
            ->leftJoin('category_translations', 'category_translations.category_id', '=', 'categories.id');

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'name':
                    $queryBuilder->where('category_translations.name', 'like', '%' . urldecode($value) . '%');

                    break;
                case 'description':
                    $queryBuilder->where('category_translations.description', 'like', '%' . urldecode($value) . '%');

                    break;
                case 'status':
                    $queryBuilder->where('categories.status', $value);

                    break;
                case 'only_children':
                    $queryBuilder->whereNotNull('categories.parent_id');

                    break;
                case 'parent_id':
                    $parentIds = array_filter(array_map('trim', explode(',', $value)));
                    $queryBuilder->whereIn('categories.parent_id', $parentIds);

                    break;
                case 'locale':
                    $queryBuilder->where('category_translations.locale', $value);

                    break;
                case 'show_in_header':
                    $queryBuilder->where('categories.show_in_header', $value);

                    break;
                case 'show_in_carousel':
                    $queryBuilder->where('categories.show_in_carousel', $value);

                    break;
            }
        }

        return $queryBuilder->paginate($params['limit'] ?? 10);
    }

    /**
     * Create category.
     *
     * @return \Webkul\Category\Contracts\Category
     */
    public function create(array $data)
    {
        if (
            isset($data['locale'])
            && $data['locale'] == 'all'
        ) {
            $model = app()->make($this->model());

            foreach (core()->getAllLocales() as $locale) {
                foreach ($model->translatedAttributes as $attribute) {
                    if (isset($data[$attribute])) {
                        $data[$locale->code][$attribute] = $data[$attribute];

                        $data[$locale->code]['locale_id'] = $locale->id;
                    }
                }
            }
        }

        $category = $this->model->create($data);

        $this->uploadImages($data, $category);

        $this->uploadImages($data, $category, 'banner_path');

        if (isset($data['attributes'])) {
            $category->filterableAttributes()->sync($data['attributes']);
        }

        return $category;
    }

    /**
     * Update category.
     *
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Category\Contracts\Category
     */
    public function update(array $data, $id)
    {
        $category = $this->find($id);

        $data = $this->setSameAttributeValueToAllLocale($data, 'slug');

        $category->update($data);

        $this->uploadImages($data, $category);

        $this->uploadImages($data, $category, 'banner_path');

        if (isset($data['attributes'])) {
            $category->filterableAttributes()->sync($data['attributes']);
        }

        return $category;
    }

    /**
     * Specify category tree.
     *
     * @return \Webkul\Category\Contracts\Category
     */
    public function getCategoryTree(?int $id = null)
    {
        return $id
            ? $this->model::orderBy('position', 'ASC')->where('id', '!=', $id)->get()->toTree()
            : $this->model::orderBy('position', 'ASC')->get()->toTree();
    }

    /**
     * Specify category tree.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCategoryTreeWithoutDescendant(?int $id = null)
    {
        return $id
            ? $this->model::orderBy('position', 'ASC')->where('id', '!=', $id)->whereNotDescendantOf($id)->get()->toTree()
            : $this->model::orderBy('position', 'ASC')->get()->toTree();
    }

    /**
     * Get root categories.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRootCategories()
    {
        return $this->getModel()->where('parent_id', null)->get();
    }

    /**
     * Get child categories.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChildCategories($parentId)
    {
        return $this->getModel()->where('parent_id', $parentId)->get();
    }

    /**
     * get visible category tree.
     *
     * @param  int  $id
     * @return \Illuminate\Support\Collection
     */
    public function getVisibleCategoryTree($id = null, array $params = [])
    {
        $queryBuilder = $this->model::orderBy('position', 'ASC')->where('status', 1);

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'show_in_header':
                case 'show_in_carousel':
                    $queryBuilder->where($key, $value);
                    break;
            }
        }

        return $id
            ? $queryBuilder->descendantsAndSelf($id)->toTree($id)
            : $queryBuilder->get()->toTree();
    }

    /**
     * Checks slug is unique or not based on locale.
     *
     * @param  int  $id
     * @param  string  $slug
     * @return bool
     */
    public function isSlugUnique($id, $slug)
    {
        $exists = CategoryTranslationProxy::modelClass()::where('category_id', '<>', $id)
            ->where('slug', $slug)
            ->limit(1)
            ->select(DB::raw(1))
            ->exists();

        return !$exists;
    }

    /**
     * Retrieve category from slug.
     *
     * @param  string  $slug
     * @return \Webkul\Category\Contracts\Category
     */
    public function findBySlug($slug)
    {
        if ($category = $this->model->whereTranslation('slug', $slug)->first()) {
            return $category;
        }
    }

    /**
     * Retrieve category from slug.
     *
     * @param  string  $slug
     * @return \Webkul\Category\Contracts\Category
     */
    public function findBySlugOrFail($slug)
    {
        return $this->model->whereTranslation('slug', $slug)->firstOrFail();
    }

    /**
     * Upload category's images.
     *
     * @param  array  $data
     * @param  \Webkul\Category\Contracts\Category  $category
     * @param  string  $type
     * @return void
     */
    public function uploadImages($data, $category, $type = 'logo_path')
    {
        if (isset($data[$type])) {
            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;

                if (request()->hasFile($file)) {
                    if ($category->{$type}) {
                        Storage::delete($category->{$type});
                    }

                    $uploadedFile = request()->file($file);

                    if ($uploadedFile->getClientOriginalExtension() == 'svg') {
                        $category->{$type} = $uploadedFile->store('category/' . $category->id);
                    } else {
                        $manager = new ImageManager;

                        $image = $manager->make($uploadedFile)->encode('webp');

                        $category->{$type} = 'category/' . $category->id . '/' . Str::random(40) . '.webp';

                        Storage::put($category->{$type}, $image);
                    }

                    $category->save();
                }
            }
        } else {
            if ($category->{$type}) {
                Storage::delete($category->{$type});
            }

            $category->{$type} = null;

            $category->save();
        }
    }

    /**
     * Get partials.
     *
     * @param  array|null  $columns
     * @return array
     */
    public function getPartial($columns = null)
    {
        $categories = $this->model->all();

        $trimmed = [];

        foreach ($categories as $key => $category) {
            if (!empty($category->name)) {
                $trimmed[$key] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }
        }

        return $trimmed;
    }

    /**
     * Set same value to all locales in category.
     *
     * To Do: Move column from the `category_translations` to `category` table. And remove
     * this created method.
     *
     * @param  string  $attributeNames
     * @return array
     */

    /**
     * Set same value to all locales in category.
     *
     * To Do: Move column from the `category_translations` to `category` table. And remove
     * this created method.
     *
     * @param  string  $attributeNames
     * @return array
     */
    private function setSameAttributeValueToAllLocale(array $data, ...$attributeNames)
    {
        $requestedLocale = core()->getRequestedLocaleCode();

        $model = app()->make($this->model());

        foreach ($attributeNames as $attributeName) {
            foreach (core()->getAllLocales() as $locale) {
                if ($requestedLocale == $locale->code) {
                    foreach ($model->translatedAttributes as $attribute) {
                        if ($attribute === $attributeName) {
                            $data[$locale->code][$attribute] = $data[$requestedLocale][$attribute] ?? $data[$data['locale']][$attribute];
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get personalized categories for customer.
     *
     * @param  \Webkul\Customer\Contracts\Customer  $customer
     * @return \Illuminate\Support\Collection
     */
    public function getPersonalizedCategoriesForCustomer($customer)
    {
        $categoryIds = collect();

        // From Wishlist
        $wishlistCategoryIds = $customer->wishlist_items()
            ->with('product.categories')
            ->get()
            ->pluck('product.categories.*.id')
            ->flatten();

        // From Orders
        $orderCategoryIds = $customer->orders()
            ->with('items.product.categories')
            ->get()
            ->pluck('items.*.product.categories.*.id')
            ->flatten();

        $categoryIds = $categoryIds->merge($wishlistCategoryIds)->merge($orderCategoryIds);

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        // Count occurrences and get top IDs
        $topCategoryIds = $categoryIds->countBy()
            ->sortDesc()
            ->take(5)
            ->keys();

        return $this->model->whereIn('id', $topCategoryIds)
            ->where('status', 1)
            ->get();
    }
}
