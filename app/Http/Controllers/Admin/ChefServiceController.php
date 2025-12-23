<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreChefServiceRequest;
use App\Http\Requests\UpdateChefServiceRequest;
use App\Services\ChefServiceService;
use App\DTOs\ChefServiceDTO;
use App\Models\ChefService;
use App\Models\Chef;
use App\Models\Tag;
use Inertia\Inertia;

class ChefServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:chef-services.view')->only(['index', 'show']);
        $this->middleware('permission:chef-services.create')->only(['create', 'store']);
        $this->middleware('permission:chef-services.update')->only(['edit', 'update', 'activate', 'deactivate']);
        $this->middleware('permission:chef-services.delete')->only(['destroy']);
    }

    public function index(Request $request, ChefServiceService $serviceService)
    {
        $perPage = $request->input('per_page', 9);
        $services = $serviceService->paginate($perPage, ['chef:id,name,logo', 'tags']);
        $services->getCollection()->transform(function ($service) {
            return ChefServiceDTO::fromModel($service)->toIndexArray();
        });
        return Inertia::render('Admin/ChefService/Index', [
            'services' => $services
        ]);
    }

    public function create()
    {
        $chefs = Chef::where('is_active', true)->get(['id', 'name']);
        $tags = Tag::where('is_active', true)->get(['id', 'name', 'slug']);

        return Inertia::render('Admin/ChefService/Create', [
            'chefs' => $chefs,
            'tags' => $tags,
        ]);
    }

    public function store(StoreChefServiceRequest $request, ChefServiceService $serviceService)
    {
        $data = $request->validated();
        $serviceService->create($data);
        return redirect()->route('admin.chef-services.index');
    }

    public function show(ChefService $chefService)
    {
        $chefService->load([
            'chef',
            'tags', 
            'images' => function($query) {
                $query->where('is_active', true)->orderBy('created_at');
            },
            'equipment' => function($query) {
                $query->orderBy('is_included', 'desc')->orderBy('created_at', 'desc');
            },
            'ratings' => function($query) {
                $query->with(['customer:id,first_name,last_name', 'booking:id,date'])
                      ->where('chef_service_ratings.is_active', true)
                      ->orderBy('chef_service_ratings.created_at', 'desc');
            }
        ]);
        $dto = ChefServiceDTO::fromModel($chefService)->toArray();
        return Inertia::render('Admin/ChefService/Show', [
            'service' => $dto,
        ]);
    }

    public function edit(ChefService $chefService)
    {
        $chefService->load([
            'tags', 
            'images' => function($query) {
                $query->where('is_active', true)->orderBy('created_at');
            },
            'equipment' => function($query) {
                $query->orderBy('is_included', 'desc')->orderBy('created_at', 'desc');
            }
        ]);
        $chefs = Chef::where('is_active', true)->get(['id', 'name']);
        $tags = Tag::where('is_active', true)->get(['id', 'name', 'slug']);

        $dto = ChefServiceDTO::fromModel($chefService)->toArray();
        return Inertia::render('Admin/ChefService/Edit', [
            'service' => $dto,
            'chefs' => $chefs,
            'tags' => $tags,
        ]);
    }

    public function update(UpdateChefServiceRequest $request, ChefServiceService $serviceService, ChefService $chefService)
    {
        $data = $request->validated();
        $serviceService->update($chefService->id, $data);
        return redirect()->route('admin.chef-services.index');
    }

    public function destroy(ChefServiceService $serviceService, ChefService $chefService)
    {
        $serviceService->delete($chefService->id);
        return redirect()->route('admin.chef-services.index');
    }

    public function activate(ChefServiceService $serviceService, $id)
    {
        $serviceService->activate($id);
        return back()->with('success', 'Chef Service activated successfully');
    }

    public function deactivate(ChefServiceService $serviceService, $id)
    {
        $serviceService->deactivate($id);
        return back()->with('success', 'Chef Service deactivated successfully');
    }
}