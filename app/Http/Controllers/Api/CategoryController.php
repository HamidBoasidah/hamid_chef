<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\DTOs\CategoryDTO;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use App\Http\Requests\UploadCategoryIconRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
// Logging removed per project request

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

    /**
     * رفع أيقونة لقسم معين
     */
    public function uploadIcon(UploadCategoryIconRequest $request, int $id, CategoryService $categoryService)
    {
        try {
            $category = $categoryService->uploadIcon($id, $request->file('icon'));
            
            return $this->successResponse(
                $category->toArray(),
                'تم رفع الأيقونة بنجاح'
            );
        } catch (ValidationException $e) {
            // validation failed during icon upload
            
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            // unexpected error during icon upload
            
            return $this->errorResponse('حدث خطأ أثناء رفع الأيقونة', 500);
        }
    }
    
    /**
     * حذف أيقونة قسم معين
     */
    public function removeIcon(int $id, CategoryService $categoryService)
    {
        try {
            $category = $categoryService->removeIcon($id);
            
            return $this->successResponse(
                $category->toArray(),
                'تم حذف الأيقونة بنجاح'
            );
        } catch (\Exception $e) {
            // unexpected error during icon removal
            
            return $this->errorResponse('حدث خطأ أثناء حذف الأيقونة', 500);
        }
    }

    /**
     * عرض تفاصيل قسم معين
     */
    public function show(int $id, CategoryService $categoryService)
    {
        try {
            $category = $categoryService->find($id);
            $categoryDTO = CategoryDTO::fromModel($category);
            
            return $this->successResponse(
                $categoryDTO->toArray(),
                'تم جلب بيانات القسم بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('القسم غير موجود', 404);
        }
    }
}
