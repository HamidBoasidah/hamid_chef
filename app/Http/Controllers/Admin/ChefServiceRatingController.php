<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ChefServiceRatingService;
use App\DTOs\ChefServiceRatingDTO;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChefServiceRatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض قائمة التقييمات
     */
    public function index(Request $request, ChefServiceRatingService $ratingService): Response
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');

        $query = $ratingService->all(['booking', 'customer', 'chef']);

        // تطبيق البحث إذا وجد
        if ($search) {
            $query = $query->filter(function ($rating) use ($search) {
                return stripos($rating->review, $search) !== false ||
                       stripos($rating->customer->first_name ?? '', $search) !== false ||
                       stripos($rating->customer->last_name ?? '', $search) !== false ||
                       stripos($rating->chef->name ?? '', $search) !== false;
            });
        }

        // تحويل إلى مجموعة مرقمة
        $ratings = $query->forPage(
            $request->get('page', 1),
            $perPage
        );

        $ratingsData = $ratings->map(function ($rating) {
            return ChefServiceRatingDTO::fromModel($rating)->toArray();
        });

        return Inertia::render('Admin/ChefServiceRatings/Index', [
            'ratings' => [
                'data' => $ratingsData,
                'current_page' => (int) $request->get('page', 1),
                'per_page' => $perPage,
                'total' => $query->count(),
            ],
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * عرض تقييم واحد
     */
    public function show(ChefServiceRatingService $ratingService, int $id): Response
    {
        $rating = $ratingService->find($id, ['booking', 'customer', 'chef']);

        return Inertia::render('Admin/ChefServiceRatings/Show', [
            'rating' => ChefServiceRatingDTO::fromModel($rating)->toArray(),
        ]);
    }

    /**
     * حذف تقييم (إدارياً فقط)
     */
    public function destroy(ChefServiceRatingService $ratingService, int $id): RedirectResponse
    {
        try {
            $ratingService->delete($id);

            return redirect()->route('admin.chef-service-ratings.index')
                ->with('success', 'تم حذف التقييم بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'فشل في حذف التقييم: ' . $e->getMessage());
        }
    }

    /**
     * تعطيل تقييم
     */
    public function deactivate(ChefServiceRatingService $ratingService, int $id): RedirectResponse
    {
        try {
            $ratingService->deactivate($id);

            return redirect()->back()
                ->with('success', 'تم تعطيل التقييم بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'فشل في تعطيل التقييم: ' . $e->getMessage());
        }
    }

    /**
     * تفعيل تقييم
     */
    public function activate(ChefServiceRatingService $ratingService, int $id): RedirectResponse
    {
        try {
            $ratingService->activate($id);

            return redirect()->back()
                ->with('success', 'تم تفعيل التقييم بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'فشل في تفعيل التقييم: ' . $e->getMessage());
        }
    }
}