<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use App\DTOs\TagDTO;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use Illuminate\Http\Request;

class TagController extends Controller
{
    use SuccessResponse, CanFilter;

    /**
     * عرض قائمة الوسوم مع فلاتر وترقيم
     */
    public function index(Request $request, TagService $tagService)
    {
        $perPage = (int) $request->get('per_page', 10);

        $query = $tagService->query();

        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $tags = $query->latest()->paginate($perPage);

        $tags->getCollection()->transform(function ($tag) {
            return TagDTO::fromModel($tag)->toIndexArray();
        });

        return $this->collectionResponse($tags, 'تم جلب قائمة الوسوم بنجاح');
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
            'is_active' => 'is_active',
        ];
    }
}
