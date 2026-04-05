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
        $attributeCode = 'short_name';
        $attributeRepository = app(AttributeRepository::class);
        $attributeFamilyRepository = app(AttributeFamilyRepository::class);
        $flatIndexer = app(FlatIndexer::class);

        // 1. Create Attribute if not exists
        $attribute = $attributeRepository->findOneByField('code', $attributeCode);

        if (! $attribute) {
            $attribute = $attributeRepository->create([
                'code'              => $attributeCode,
                'admin_name'        => 'Short Name',
                'type'              => 'text',
                'validation'        => null,
                'position'          => 100,
                'is_required'       => 0,
                'is_unique'         => 0,
                'is_filterable'     => 1,
                'is_configurable'   => 0,
                'is_user_defined'   => 1,
                'is_visible_on_front' => 1,
                'use_in_flat'       => 1,
                'name'              => [
                    'en' => 'Short Name',
                    'ru' => 'Короткое название',
                ],
            ]);
        }

        // 2. Assign to all Attribute Families in 'General' group
        $families = $attributeFamilyRepository->all();

        foreach ($families as $family) {
            $group = $family->attribute_groups()->where('code', 'general')->first() 
                  ?: $family->attribute_groups()->where('name', 'General')->first();

            if ($group) {
                // Check if already mapped
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

        // 3. Sync Flat Table Schema
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
        // Typically we don't delete attributes in down() to avoid data loss, 
        // but for completeness:
        // $attribute = app(AttributeRepository::class)->findOneByField('code', 'short_name');
        // if ($attribute) $attribute->delete();
    }
};
