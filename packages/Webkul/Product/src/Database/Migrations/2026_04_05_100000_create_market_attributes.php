<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Webkul\Product\Helpers\Indexers\Flat as FlatIndexer;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $attributeRepository = app(AttributeRepository::class);
        $attributeFamilyRepository = app(AttributeFamilyRepository::class);
        $flatIndexer = app(FlatIndexer::class);

        $attributes = [
            [
                'code'       => 'short_name',
                'admin_name' => 'Short Name',
                'type'       => 'text',
                'name'       => ['en' => 'Short Name', 'ru' => 'Короткое название'],
            ],
            [
                'code'       => 'nominal',
                'admin_name' => 'Nominal',
                'type'       => 'price',
                'name'       => ['en' => 'Nominal', 'ru' => 'Номинал'],
            ],
            [
                'code'       => 'activation_period',
                'admin_name' => 'Activation Period',
                'type'       => 'text',
                'name'       => ['en' => 'Activation Period', 'ru' => 'Срок активации'],
            ],
            [
                'code'       => 'purpose',
                'admin_name' => 'Purpose',
                'type'       => 'text',
                'name'       => ['en' => 'Purpose', 'ru' => 'Назначение'],
            ],
            [
                'code'       => 'type_of_product',
                'admin_name' => 'Type of Product',
                'type'       => 'text',
                'name'       => ['en' => 'Type of Product', 'ru' => 'Тип товара'],
            ],
            [
                'code'       => 'region',
                'admin_name' => 'Region',
                'type'       => 'text',
                'name'       => ['en' => 'Region', 'ru' => 'Регион'],
            ],
        ];

        foreach ($attributes as $attrData) {
            $attribute = $attributeRepository->findOneByField('code', $attrData['code']);

            if (! $attribute) {
                $attribute = $attributeRepository->create(array_merge([
                    'validation'          => null,
                    'position'            => 100,
                    'is_required'         => 0,
                    'is_unique'           => 0,
                    'is_filterable'       => 1,
                    'is_configurable'     => 0,
                    'is_user_defined'     => 1,
                    'is_visible_on_front' => 1,
                    'use_in_flat'       => 1,
                ], $attrData));
            }

            // Assign to all Attribute Families in 'General' group
            $families = $attributeFamilyRepository->all();

            foreach ($families as $family) {
                $group = $family->attribute_groups()->where('code', 'general')->first() 
                      ?: $family->attribute_groups()->where('name', 'General')->first();

                if ($group) {
                    $exists = DB::table('attribute_group_mappings')
                        ->where('attribute_group_id', $group->id)
                        ->where('attribute_id', $attribute->id)
                        ->exists();

                    if (! $exists) {
                        DB::table('attribute_group_mappings')->insert([
                            'attribute_group_id' => $group->id,
                            'attribute_id'       => $attribute->id,
                        ]);
                    }
                }
            }
        }

        // Sync Flat Table Schema
        try {
            $flatIndexer->createTable();
        } catch (\Exception $e) {
            // Log or ignore if table update fails during migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
