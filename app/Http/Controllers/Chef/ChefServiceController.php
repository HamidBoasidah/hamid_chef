<?php

namespace App\Http\Controllers\Chef;

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
        $this->middleware('auth:web');
    }

    public function index(Request $request, ChefServiceService $serviceService)
    {
        $this->authorize('viewAny', ChefService::class);

        $user = $request->user();
        $perPage = $request->input('per_page', 9);
        
        // Filter services by authenticated chef's ID
        $services = $serviceService->query(['chef:id,name,logo', 'tags'])
            ->where('chef_id', $user->chef->id)
            ->latest()
            ->paginate($perPage);
        
        $services->getCollection()->transform(function ($service) {
            return ChefServiceDTO::fromModel($service)->toIndexArray();
        });
        
        return Inertia::render('Chef/ChefService/Index', [
            'services' => $services
        ]);
    }

    public function create()
    {
        $this->authorize('create', ChefService::class);

        $user = auth('web')->user();
        // Show only the authenticated chef
        $chefs = Chef::where('id', $user->chef->id)->get(['id', 'name']);
        $tags = Tag::where('is_active', true)->get(['id', 'name', 'slug']);

        return Inertia::render('Chef/ChefService/Create', [
            'chefs' => $chefs,
            'tags' => $tags,
        ]);
    }

    public function store(StoreChefServiceRequest $request, ChefServiceService $serviceService)
    {
        $this->authorize('create', ChefService::class);

        $user = $request->user();
        $data = $request->validated();
        // Force chef_id to authenticated chef
        $data['chef_id'] = $user->chef->id;
        
        $serviceService->create($data);
        return redirect()->route('chef-services.index');
    }

    public function show(ChefService $chefService)
    {
        $this->authorize('view', $chefService);

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
        return Inertia::render('Chef/ChefService/Show', [
            'service' => $dto,
        ]);
    }

    public function edit(ChefService $chefService)
    {
        $this->authorize('update', $chefService);

        $chefService->load([
            'tags', 
            'images' => function($query) {
                $query->where('is_active', true)->orderBy('created_at');
            },
            'equipment' => function($query) {
                $query->orderBy('is_included', 'desc')->orderBy('created_at', 'desc');
            }
        ]);
        $user = auth('web')->user();
        // Show only the authenticated chef
        $chefs = Chef::where('id', $user->chef->id)->get(['id', 'name']);
        $tags = Tag::where('is_active', true)->get(['id', 'name', 'slug']);

        $dto = ChefServiceDTO::fromModel($chefService)->toArray();
        return Inertia::render('Chef/ChefService/Edit', [
            'service' => $dto,
            'chefs' => $chefs,
            'tags' => $tags,
        ]);
    }

    public function update(UpdateChefServiceRequest $request, ChefServiceService $serviceService, ChefService $chefService)
    {
        $this->authorize('update', $chefService);

        $data = $request->validated();
        $serviceService->update($chefService->id, $data);
        return redirect()->route('chef-services.index');
    }

    public function destroy(ChefServiceService $serviceService, ChefService $chefService)
    {
        $this->authorize('delete', $chefService);

        $serviceService->delete($chefService->id);
        return redirect()->route('chef-services.index');
    }

    public function activate(ChefServiceService $serviceService, $id)
    {
        $chefService = ChefService::findOrFail($id);
        $this->authorize('activate', $chefService);

        $serviceService->activate($id);
        return back()->with('success', 'Chef Service activated successfully');
    }

    public function deactivate(ChefServiceService $serviceService, $id)
    {
        $chefService = ChefService::findOrFail($id);
        $this->authorize('deactivate', $chefService);

        $serviceService->deactivate($id);
        return back()->with('success', 'Chef Service deactivated successfully');
    }
}