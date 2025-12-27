<?php

namespace App\DTOs;

use App\Models\LandingPageSection;

class LandingPageSectionDTO extends BaseDTO
{
    public $id;
    public $section_key;
    public $title_ar;
    public $title_en;
    public $description_ar;
    public $description_en;
    public $image;
    public $display_order;
    public $additional_data;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    public function __construct(
        $id,
        $section_key,
        $title_ar,
        $title_en,
        $description_ar,
        $description_en,
        $image,
        $display_order,
        $additional_data,
        $is_active,
        $created_by = null,
        $updated_by = null,
        $created_at = null,
        $updated_at = null
    ) {
        $this->id = $id;
        $this->section_key = $section_key;
        $this->title_ar = $title_ar;
        $this->title_en = $title_en;
        $this->description_ar = $description_ar;
        $this->description_en = $description_en;
        $this->image = $image;
        $this->display_order = $display_order;
        $this->additional_data = $additional_data;
        $this->is_active = $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public static function fromModel(LandingPageSection $m): self
    {
        return new self(
            $m->id,
            $m->section_key,
            $m->title_ar,
            $m->title_en,
            $m->description_ar,
            $m->description_en,
            $m->image,
            $m->display_order,
            $m->additional_data,
            $m->is_active,
            $m->created_by ?? null,
            $m->updated_by ?? null,
            $m->created_at,
            $m->updated_at
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'section_key' => $this->section_key,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'image' => $this->image,
            'image_url' => $this->fileUrl($this->image),
            'display_order' => $this->display_order,
            'additional_data' => $this->additional_data,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function toIndexArray(): array
    {
        return [
            'id' => $this->id,
            'section_key' => $this->section_key,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'image' => $this->image,
            'image_url' => $this->fileUrl($this->image),
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
        ];
    }
}
