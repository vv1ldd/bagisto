<?php

namespace Webkul\Shop\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'position' => $this->position,
            'display_mode' => $this->display_mode,
            'description' => $this->description,
            'logo' => $this->when($this->logo_path, fn() => $this->buildImageUrls($this->logo_path)),
            'banner' => $this->when($this->banner_path, fn() => $this->buildImageUrls($this->banner_path)),
            'meta' => [
                'title' => $this->meta_title,
                'keywords' => $this->meta_keywords,
                'description' => $this->meta_description,
            ],
            'translations' => $this->translations,
            'additional' => $this->additional,
        ];
    }

    /**
     * Build image URLs. For SVGs, return direct storage URLs (Intervention
     * Image Cache cannot process vector images). For rasters, return cached
     * URLs as usual.
     *
     * @param  string  $path
     * @return array
     */
    protected function buildImageUrls(?string $path): array
    {
        if ($path && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'svg') {
            $directUrl = url('storage/' . $path);

            return [
                'small_image_url' => $directUrl,
                'medium_image_url' => $directUrl,
                'large_image_url' => $directUrl,
                'original_image_url' => $directUrl,
            ];
        }

        return [
            'small_image_url' => url('cache/small/' . $path),
            'medium_image_url' => url('cache/medium/' . $path),
            'large_image_url' => url('cache/large/' . $path),
            'original_image_url' => url('cache/original/' . $path),
        ];
    }
}
