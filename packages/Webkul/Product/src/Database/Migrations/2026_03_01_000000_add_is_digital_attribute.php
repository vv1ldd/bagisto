<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds a boolean `is_digital` attribute to all attribute families.
 * When checked on a product, the checkout shipping step is skipped.
 */
return new class extends Migration {
    public function up(): void
    {
        // 1. Check if the attribute already exists
        if (DB::table('attributes')->where('code', 'is_digital')->exists()) {
            return;
        }

        // 2. Insert the attribute
        $attributeId = DB::table('attributes')->insertGetId([
            'code' => 'is_digital',
            'type' => 'boolean',
            'validation' => null,
            'position' => 30,
            'is_required' => 0,
            'is_unique' => 0,
            'value_per_locale' => 0,
            'value_per_channel' => 0,
            'is_filterable' => 0,
            'is_configurable' => 0,
            'is_user_defined' => 1,
            'is_visible_on_front' => 0,
            'enable_wysiwyg' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Insert translations (Russian label)
        DB::table('attribute_translations')->insert([
            'attribute_id' => $attributeId,
            'locale' => 'ru',
            'name' => 'Цифровой товар',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // English fallback
        DB::table('attribute_translations')->insert([
            'attribute_id' => $attributeId,
            'locale' => 'en',
            'name' => 'Digital Product',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Map to every attribute family's "General" (or first) group
        $families = DB::table('attribute_families')->pluck('id');

        foreach ($families as $familyId) {
            // Prefer a group named "General" or "general", otherwise take the first group
            $group = DB::table('attribute_groups')
                ->where('attribute_family_id', $familyId)
                ->where(function ($q) {
                    $q->whereRaw('LOWER(code) = ?', ['general'])
                        ->orWhereRaw('LOWER(name) = ?', ['general']);
                })
                ->first();

            if (!$group) {
                $group = DB::table('attribute_groups')
                    ->where('attribute_family_id', $familyId)
                    ->orderBy('position')
                    ->first();
            }

            if (!$group) {
                continue;
            }

            $maxPosition = DB::table('attribute_group_mappings')
                ->where('attribute_group_id', $group->id)
                ->max('position') ?? 0;

            DB::table('attribute_group_mappings')->insertOrIgnore([
                'attribute_id' => $attributeId,
                'attribute_group_id' => $group->id,
                'position' => $maxPosition + 1,
            ]);
        }
    }

    public function down(): void
    {
        $attr = DB::table('attributes')->where('code', 'is_digital')->first();

        if (!$attr) {
            return;
        }

        DB::table('attribute_group_mappings')->where('attribute_id', $attr->id)->delete();
        DB::table('attribute_translations')->where('attribute_id', $attr->id)->delete();
        DB::table('product_attribute_values')->where('attribute_id', $attr->id)->delete();
        DB::table('attributes')->where('id', $attr->id)->delete();
    }
};
