<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\LandingPageSectionService;
use Inertia\Inertia;
use Inertia\Response;

class LandingPageController extends Controller
{
    /**
     * Display the public landing page with all active sections
     */
    public function index(LandingPageSectionService $service): Response
    {
        // Get all active sections
        $sections = $service->builder()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Transform sections into a keyed array for easy access
        $sectionsData = [];
        foreach ($sections as $section) {
            $sectionsData[$section->section_key] = [
                'id' => $section->id,
                'section_key' => $section->section_key,
                'title_ar' => $section->title_ar,
                'title_en' => $section->title_en,
                'description_ar' => $section->description_ar,
                'description_en' => $section->description_en,
                'image' => $section->image,
                'display_order' => $section->display_order,
                'additional_data' => $section->additional_data,
                'is_active' => $section->is_active,
            ];
        }

        // Get current locale from session or default to 'ar'
        $locale = session('locale', config('app.locale', 'ar'));

        return Inertia::render('Site/LandingPage', [
            'sections' => $sectionsData,
            'locale' => $locale,
        ]);
    }
}
