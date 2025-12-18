<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\DTOs\CategoryDTO;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:categories.view')->only(['index', 'show']);
        $this->middleware('permission:categories.create')->only(['create', 'store']);
        $this->middleware('permission:categories.update')->only(['edit', 'update', 'activate', 'deactivate']);
        $this->middleware('permission:categories.delete')->only(['destroy']);
    }

    public function index(Request $request, CategoryService $categoryService)
    {
        $perPage = $request->input('per_page', 10);
        $categories = $categoryService->paginate($perPage);
        $categories->getCollection()->transform(function ($category) {
            return CategoryDTO::fromModel($category)->toIndexArray();
        });
        return Inertia::render('Admin/Category/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Category/Create');
    }

    public function store(StoreCategoryRequest $request, CategoryService $categoryService)
    {
        $data = $request->validated();
        $categoryService->create($data);
        return redirect()->route('admin.categories.index');
    }

    public function show(Category $category)
    {
        $dto = CategoryDTO::fromModel($category)->toArray();
        return Inertia::render('Admin/Category/Show', [
            'category' => $dto,
        ]);
    }

    public function edit(Category $category)
    {
        $dto = CategoryDTO::fromModel($category)->toArray();
        return Inertia::render('Admin/Category/Edit', [
            'category' => $dto,
        ]);
    }

    public function update(UpdateCategoryRequest $request, CategoryService $categoryService, Category $category)
    {
        $data = $request->validated();
        $categoryService->update($category->id, $data);
        return redirect()->route('admin.categories.index');
    }

    public function destroy(CategoryService $categoryService, Category $category)
    {
        $categoryService->delete($category->id);
        return redirect()->route('admin.categories.index');
    }

    public function activate(CategoryService $categoryService, $id)
    {
        $categoryService->activate($id);
        return back()->with('success', 'Category activated successfully');
    }

    public function deactivate(CategoryService $categoryService, $id)
    {
        $categoryService->deactivate($id);
        return back()->with('success', 'Category deactivated successfully');
    }
}
