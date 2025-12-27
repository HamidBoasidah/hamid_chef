<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\LandingPageSectionService;
use App\DTOs\LandingPageSectionDTO;
use App\Http\Requests\StoreLandingPageSectionRequest;
use App\Http\Requests\UpdateLandingPageSectionRequest;
use App\Models\LandingPageSection;
use Illuminate\Support\Str;

class LandingPageSectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:landing-page-sections.view')->only(['index', 'show']);
        $this->middleware('permission:landing-page-sections.create')->only(['create', 'store']);
        $this->middleware('permission:landing-page-sections.update')->only(['edit', 'update', 'manage', 'activate', 'deactivate']);
        $this->middleware('permission:landing-page-sections.delete')->only(['destroy']);
    }

    public function index(Request $request, LandingPageSectionService $service)
    {
        $perPage = $request->input('per_page', 10);
        $sections = $service->paginate($perPage, ['createdBy', 'updatedBy']);

        $sections->getCollection()->transform(function ($section) {
            return LandingPageSectionDTO::fromModel($section)->toIndexArray();
        });

        return Inertia::render('Admin/LandingPageSection/Index', [
            'landing_page_sections' => $sections,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/LandingPageSection/Create');
    }

    public function manage(string $sectionKey, LandingPageSectionService $service)
    {
        $section = $service->getBySectionKey($sectionKey);

        if (!$section) {
            $section = $service->create([
                'section_key' => $sectionKey,
                'title_en' => ucfirst(str_replace('_', ' ', $sectionKey)),
                'title_ar' => $sectionKey,
                'is_active' => true,
            ]);
        }

        $dto = LandingPageSectionDTO::fromModel($section)->toArray();

        $view = $this->resolveSectionView($sectionKey);

        $props = [
            'section' => $dto,
        ];

        return Inertia::render($view, $props);
    }

    public function store(StoreLandingPageSectionRequest $request, LandingPageSectionService $service)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        
        $service->create($data);

        // Keep the admin inside the section editor flow
        $sectionKey = $data['section_key'] ?? null;
        if (is_string($sectionKey) && $sectionKey !== '') {
            return redirect()->route('admin.landing-page-sections.manage', ['section' => $sectionKey])
                ->with('success', 'Landing page section created successfully');
        }

        return back()->with('success', 'Landing page section created successfully');
    }

    public function show(LandingPageSection $landing_page_section)
    {
        $dto = LandingPageSectionDTO::fromModel($landing_page_section)->toArray();
        
        return Inertia::render('Admin/LandingPageSection/Show', [
            'landing_page_section' => $dto,
        ]);
    }

    public function edit(LandingPageSection $landing_page_section)
    {
        $dto = LandingPageSectionDTO::fromModel($landing_page_section)->toArray();
        
        return Inertia::render('Admin/LandingPageSection/Edit', [
            'landing_page_section' => $dto,
        ]);
    }

    public function update(UpdateLandingPageSectionRequest $request, LandingPageSectionService $service, LandingPageSection $landing_page_section)
    {
        \Log::info('🔄 LandingPageSectionController::update started', [
            'section_id' => $landing_page_section->id,
            'section_key' => $landing_page_section->section_key,
            'request_data' => $request->all()
        ]);

        $data = $request->validated();

        \Log::info('📋 Validated data:', $data);

        // Handle additional_data - decode if it's a JSON string
        if (isset($data['additional_data']) && is_string($data['additional_data'])) {
            $decoded = json_decode($data['additional_data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['additional_data'] = $decoded;
                \Log::info('📋 Decoded additional_data from JSON string');
            }
        }

        // Handle hero slider images sent as slide_images[index]
        if (isset($data['additional_data']['slides']) && is_array($data['additional_data']['slides'])) {
            $slides = $data['additional_data']['slides'];
            $uploadedSlides = $request->file('slide_images', []);

            \Log::info('🖼️ Processing hero slides:', [
                'slides_count' => count($slides),
                'uploaded_images_count' => count($uploadedSlides)
            ]);

            foreach ($uploadedSlides as $index => $file) {
                if (!isset($slides[$index]) || !$file instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }

                $path = $file->store('landing-sections', 'public');
                $slides[$index]['image'] = $path;
                unset($slides[$index]['has_new_image']);
                \Log::info("📸 Uploaded slide image {$index}: {$path}");
            }

            $data['additional_data']['slides'] = $slides;
            \Log::info('✅ Hero slides processed successfully');
        }

        // Handle partner logos sent as partner_logos[index]
        if (isset($data['additional_data']['items']) && is_array($data['additional_data']['items'])) {
            $items = $data['additional_data']['items'];
            $uploadedLogos = $request->file('partner_logos', []);

            \Log::info('🖼️ Processing partner logos:', [
                'items_count' => count($items),
                'uploaded_logos_count' => count($uploadedLogos)
            ]);

            foreach ($uploadedLogos as $index => $file) {
                if (!isset($items[$index]) || !$file instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }

                $path = $file->store('partners', 'public');
                $items[$index]['logo'] = $path;
                unset($items[$index]['has_new_logo']);
                \Log::info("📸 Uploaded partner logo {$index}: {$path}");
            }

            $data['additional_data']['items'] = $items;
            \Log::info('✅ Partner logos processed successfully');
        }

        // Handle image uploads - check for files first, then check for removal indicators
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        } elseif ($request->has('image') && ($request->input('image') === null || $request->input('image') === '')) {
            // Explicit removal request
            $data['image'] = null;
        }
        
        \Log::info('📤 Calling service update with data:', $data);
        
        $result = $service->update($landing_page_section->id, $data);
        
        \Log::info('✅ Service update completed', ['result' => $result ? 'success' : 'failed']);

        // Stay on the same manage page after saving
        return redirect()->route('admin.landing-page-sections.manage', ['section' => $landing_page_section->section_key])
            ->with('success', 'Landing page section updated successfully');
    }

    public function destroy(LandingPageSectionService $service, LandingPageSection $landing_page_section)
    {
        $service->delete($landing_page_section->id);

        return back()->with('success', 'Landing page section deleted successfully');
    }

    public function activate(LandingPageSectionService $service, $id)
    {
        $service->activate($id);
        
        return back()->with('success', 'Landing page section activated successfully');
    }

    public function deactivate(LandingPageSectionService $service, $id)
    {
        $service->deactivate($id);
        
        return back()->with('success', 'Landing page section deactivated successfully');
    }

    private function resolveSectionView(string $sectionKey): string
    {
        $map = [
            'hero' => 'Admin/LandingPageSection/Hero',
            'features' => 'Admin/LandingPageSection/Features',
            'how_it_works' => 'Admin/LandingPageSection/HowItWorks',
            'top_chefs' => 'Admin/LandingPageSection/TopChefs',
            'categories' => 'Admin/LandingPageSection/Categories',
            'testimonials' => 'Admin/LandingPageSection/Testimonials',
            'about_us' => 'Admin/LandingPageSection/AboutUs',
            'vision_mission' => 'Admin/LandingPageSection/VisionMission',
            'why_us' => 'Admin/LandingPageSection/WhyUs',
            'partners' => 'Admin/LandingPageSection/Partners',
            'contact' => 'Admin/LandingPageSection/Contact',
        ];

        if (isset($map[$sectionKey])) {
            return $map[$sectionKey];
        }

        // Try to map any new section_key to a matching Inertia page (e.g. footer -> Admin/LandingPageSection/Footer)
        $guessed = 'Admin/LandingPageSection/' . Str::studly($sectionKey);

        return $guessed;
    }
}
