<?php

namespace App\Services;

use App\Repositories\LandingPageSectionRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class LandingPageSectionService
{
    protected LandingPageSectionRepository $sections;

    public function __construct(LandingPageSectionRepository $sections)
    {
        $this->sections = $sections;
    }

    public function builder(array $with = []): Builder
    {
        return $this->sections->query($with);
    }

    public function all(array $with = [])
    {
        return $this->sections->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->sections->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->sections->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        // Handle additional_data as JSON string if needed
        if (isset($attributes['additional_data']) && is_string($attributes['additional_data'])) {
            $decoded = json_decode($attributes['additional_data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $attributes['additional_data'] = $decoded;
            }
        }

        if (isset($attributes['image']) && $attributes['image'] instanceof \Illuminate\Http\UploadedFile) {
            $attributes['image'] = $attributes['image']->store('landing-sections', 'public');
        }

        // Set created_by to current admin user ID
        $attributes['created_by'] = auth('admin')->id();
        
        return $this->sections->create($attributes);
    }

    public function update($id, array $attributes)
    {
        \Log::info('🔄 LandingPageSectionService::update started', [
            'id' => $id,
            'attributes' => $attributes
        ]);

        $section = $this->sections->findOrFail($id);

        \Log::info('📋 Found section:', [
            'id' => $section->id,
            'section_key' => $section->section_key,
            'current_title_ar' => $section->title_ar,
            'current_title_en' => $section->title_en
        ]);

        // Handle additional_data as JSON string if needed
        if (isset($attributes['additional_data']) && is_string($attributes['additional_data'])) {
            \Log::info('📋 Service: additional_data is string, decoding...', [
                'raw' => $attributes['additional_data']
            ]);
            $decoded = json_decode($attributes['additional_data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $attributes['additional_data'] = $decoded;
                \Log::info('✅ Service: decoded additional_data', [
                    'decoded' => $decoded,
                    'reasons_count' => isset($decoded['reasons']) ? count($decoded['reasons']) : 0,
                    'stats_count' => isset($decoded['stats']) ? count($decoded['stats']) : 0
                ]);
            } else {
                \Log::error('❌ Service: JSON decode error', [
                    'error' => json_last_error_msg()
                ]);
            }
        } elseif (isset($attributes['additional_data'])) {
            \Log::info('📋 Service: additional_data is already array', [
                'type' => gettype($attributes['additional_data']),
                'data' => $attributes['additional_data']
            ]);
        }

        // Handle image upload or removal
        if (array_key_exists('image', $attributes)) {
            if ($attributes['image'] instanceof \Illuminate\Http\UploadedFile) {
                // New image uploaded
                if ($section->image) {
                    Storage::disk('public')->delete($section->image);
                }
                $attributes['image'] = $attributes['image']->store('landing-sections', 'public');
            } elseif ($attributes['image'] === null || $attributes['image'] === '') {
                // Image removal requested
                if ($section->image) {
                    Storage::disk('public')->delete($section->image);
                }
                $attributes['image'] = null;
            } else {
                // Keep existing image, remove from attributes to avoid overwriting
                unset($attributes['image']);
            }
        }

        // Set updated_by to current admin user ID
        $attributes['updated_by'] = auth('admin')->id();

        \Log::info('📤 Final attributes for update:', $attributes);

        $result = $this->sections->update($id, $attributes);

        \Log::info('✅ Repository update completed', ['result' => $result ? 'success' : 'failed']);

        return $result;
    }

    public function delete($id)
    {
        $section = $this->sections->findOrFail($id);
        
        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }

        return $this->sections->delete($id);
    }

    public function activate($id)
    {
        return $this->sections->activate($id);
    }

    public function deactivate($id)
    {
        return $this->sections->deactivate($id);
    }

    public function getActiveOrdered()
    {
        return $this->sections->getActiveOrdered();
    }

    public function getBySectionKey(string $key)
    {
        return $this->sections->getBySectionKey($key);
    }

    public function findByKey(string $key)
    {
        return $this->sections->findByKey($key);
    }
}
