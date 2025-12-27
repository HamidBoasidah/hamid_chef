<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandingPageSectionRequest extends FormRequest
{
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
        return [
            'section_key' => 'required|string|max:255|unique:landing_page_sections,section_key',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
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
            'is_active' => 'nullable|boolean',
        ];
    }
}
