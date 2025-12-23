<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\SVGIconService;

class CategoryObserver
{
    protected SVGIconService $svgIconService;

    public function __construct(SVGIconService $svgIconService)
    {
        $this->svgIconService = $svgIconService;
    }

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        // حذف الأيقونة المرتبطة بالقسم عند حذفه
        if ($category->icon_path) {
            try {
                $this->svgIconService->deleteIcon($category->icon_path);
            } catch (\Exception $e) {
                // failed to delete category icon automatically
            }
        }
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        // حذف الأيقونة المرتبطة بالقسم عند الحذف النهائي
        if ($category->icon_path) {
            try {
                $this->svgIconService->deleteIcon($category->icon_path);
            } catch (\Exception $e) {
                // failed to force delete category icon automatically
            }
        }
    }
}
