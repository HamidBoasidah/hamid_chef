<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\CanFilter;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Services\LandingPageSectionService;
use App\DTOs\LandingPageSectionDTO;

/**
 * Landing Page Sections API Controller
 * 
 * Provides public access to landing page sections with translations
 */
class LandingPageSectionController extends Controller
{
    use CanFilter, ExceptionHandler, SuccessResponse;

    /**
     * Get all active landing page sections
     * 
     * @param Request $request
     * @param LandingPageSectionService $service
     * @return \Illuminate\Http\JsonResponse
     * 
     * @response {
     *   "success": true,
     *   "message": "تم جلب أقسام الصفحة الرئيسية بنجاح",
     *   "data": [...]
     * }
     */
    public function index(Request $request, LandingPageSectionService $service)
    {
        $query = $service->builder();

        // Filter active sections only
        $query->where('is_active', true);

        // Filter by section_key if provided
        if ($request->has('section_key')) {
            $query->where('section_key', $request->input('section_key'));
        }

        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        // Order by display_order
        $query->orderBy('display_order');

        $sections = $query->get();

        $data = $sections->map(function ($section) {
            return LandingPageSectionDTO::fromModel($section)->toArray();
        });

        return $this->successResponse(
            $data,
            'تم جلب أقسام الصفحة الرئيسية بنجاح'
        );
    }
}
