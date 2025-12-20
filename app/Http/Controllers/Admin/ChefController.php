<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreChefRequest;
use App\Http\Requests\UpdateChefRequest;
use App\Services\ChefService;
use App\DTOs\ChefDTO;
use App\Models\Chef;
use App\Models\Governorate;
use App\Models\District;
use App\Models\Area;
use App\Models\User;
use App\Models\Category;
use Inertia\Inertia;

class ChefController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:chefs.view')->only(['index', 'show']);
        $this->middleware('permission:chefs.create')->only(['create', 'store']);
        $this->middleware('permission:chefs.update')->only(['edit', 'update', 'activate', 'deactivate']);
        $this->middleware('permission:chefs.delete')->only(['destroy']);
    }

    public function index(Request $request, ChefService $chefService)
    {
        $perPage = $request->input('per_page', 10);
        $chefs = $chefService->paginate($perPage, ['categories']);
        $chefs->getCollection()->transform(function ($chef) {
            return ChefDTO::fromModel($chef)->toIndexArray();
        });
        return Inertia::render('Admin/Chef/Index', [
            'chefs' => $chefs
        ]);
    }

    public function create()
    {
        $governorates = Governorate::all(['id', 'name_ar', 'name_en']);
        $districts = District::all(['id', 'name_ar', 'name_en']);
        $areas = Area::all(['id', 'name_ar', 'name_en']);
        $users = User::all(['id', 'first_name', 'last_name', 'email']);
        $categories = Category::where('is_active', true)->get(['id', 'name', 'slug']);

        return Inertia::render('Admin/Chef/Create', [
            'governorates' => $governorates,
            'districts' => $districts,
            'areas' => $areas,
            'users' => $users,
            'categories' => $categories,
        ]);
    }

    public function store(StoreChefRequest $request, ChefService $chefService)
    {
        $data = $request->validated();
        $chefService->create($data);
        return redirect()->route('admin.chefs.index');
    }

    public function show(Chef $chef)
    {
        $chef->load(['categories', 'gallery' => function($query) {
            $query->where('is_active', true)->orderBy('created_at');
        }]);
        $dto = ChefDTO::fromModel($chef)->toArray();
        return Inertia::render('Admin/Chef/Show', [
            'chef' => $dto,
        ]);
    }

    public function edit(Chef $chef)
    {
        $chef->load(['categories', 'gallery' => function($query) {
            $query->where('is_active', true)->orderBy('created_at');
        }]);
        $governorates = Governorate::all(['id', 'name_ar', 'name_en']);
        // For edit form we only need districts/areas relevant to the chef's current selection
        $districts = District::where('governorate_id', $chef->governorate_id)->get(['id', 'name_ar', 'name_en']);
        $areas = Area::where('district_id', $chef->district_id)->get(['id', 'name_ar', 'name_en']);
        $users = User::all(['id', 'first_name', 'last_name', 'email']);
        $categories = Category::where('is_active', true)->get(['id', 'name', 'slug']);

        $dto = ChefDTO::fromModel($chef)->toArray();
        return Inertia::render('Admin/Chef/Edit', [
            'chef' => $dto,
            'governorates' => $governorates,
            'districts' => $districts,
            'areas' => $areas,
            'users' => $users,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateChefRequest $request, ChefService $chefService, Chef $chef)
    {
        $data = $request->validated();
        $chefService->update($chef->id, $data);
        return redirect()->route('admin.chefs.index');
    }

    public function destroy(ChefService $chefService, Chef $chef)
    {
        $chefService->delete($chef->id);
        return redirect()->route('admin.chefs.index');
    }

    public function activate(ChefService $chefService, $id)
    {
        $chefService->activate($id);
        return back()->with('success', 'Chef activated successfully');
    }

    public function deactivate(ChefService $chefService, $id)
    {
        $chefService->deactivate($id);
        return back()->with('success', 'Chef deactivated successfully');
    }


}
