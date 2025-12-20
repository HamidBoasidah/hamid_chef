<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\DTOs\CategoryDTO;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use SuccessResponse, CanFilter;

    /**
     * عرض قائمة التصنيفات مع فلاتر وترقيم
     */
    public function index(Request $request, CategoryService $categoryService)
    {
        $perPage = (int) $request->get('per_page', 10);

        $query = $categoryService->query();

        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $categories = $query->latest()->paginate($perPage);

        $categories->getCollection()->transform(function ($category) {
            return CategoryDTO::fromModel($category)->toIndexArray();
        });

        return $this->collectionResponse($categories, 'تم جلب قائمة التصنيفات بنجاح');
    }

    protected function getSearchableFields(): array
    {
        return [
            'name',
            'slug',
            'description',
        ];
    }

    protected function getForeignKeyFilters(): array
    {
        return [
            'parent_id' => 'parent_id',
            'is_active' => 'is_active',
        ];
    }
}
