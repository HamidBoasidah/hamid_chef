<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingPageSectionRequest extends FormRequest
{
    /**
     * Normalize incoming data before validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure section_key is always present on update.
        // Many admin forms update only partial fields (e.g. image/additional_data) and may omit section_key.
        if (!$this->filled('section_key')) {
            $routeParam = $this->route('landing_page_section');
            $sectionKey = is_object($routeParam) ? ($routeParam->section_key ?? null) : null;

            if (is_string($sectionKey) && $sectionKey !== '') {
                $this->merge([
                    'section_key' => $sectionKey,
                ]);
            }
        }

        // If additional_data arrives as JSON string (from FormData), decode it to array
        if (is_string($this->additional_data)) {
            $decoded = json_decode($this->additional_data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge([
                    'additional_data' => $decoded,
                ]);
            }
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $routeParam = $this->route('landing_page_section');
        $id = is_object($routeParam) ? ($routeParam->id ?? null) : $routeParam;
        
        return [
            'section_key' => 'sometimes|required|string|max:255|unique:landing_page_sections,section_key,' . $id,
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'display_order' => 'nullable|integer|min:0',
            'additional_data' => 'nullable|array',
            'additional_data.slides' => 'nullable|array',
            'additional_data.slides.*' => 'nullable|array',
            'additional_data.items' => 'nullable|array',
            'additional_data.items.*' => 'nullable|array',
            'additional_data.features' => 'nullable|array',
            'additional_data.features.*' => 'nullable|array',
            'additional_data.reasons' => 'nullable|array',
            'additional_data.reasons.*' => 'nullable|array',
            'additional_data.stats' => 'nullable|array',
            'additional_data.stats.*' => 'nullable|array',
            'additional_data.steps' => 'nullable|array',
            'additional_data.steps.*' => 'nullable|array',
            'additional_data.testimonials' => 'nullable|array',
            'additional_data.testimonials.*' => 'nullable|array',
            'additional_data.values' => 'nullable|array',
            'additional_data.values.*' => 'nullable|array',
            'additional_data.goals' => 'nullable|array',
            'additional_data.goals.*' => 'nullable|array',
            'additional_data.partners' => 'nullable|array',
            'additional_data.partners.*' => 'nullable|array',
            'additional_data.partnership_benefits' => 'nullable|array',
            'additional_data.partnership_benefits.*' => 'nullable|array',
            'additional_data.social_links' => 'nullable|array',
            'additional_data.social_links.*' => 'nullable|array',

            // Hero slider uploads (sent as slide_images[index])
            'slide_images' => 'sometimes|array',
            'slide_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            
            // Partner logos uploads (sent as partner_logos[index])
            'partner_logos' => 'sometimes|array',
            'partner_logos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            
            'is_active' => 'nullable|boolean',
        ];
    }
}
