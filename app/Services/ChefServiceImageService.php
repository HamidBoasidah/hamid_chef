<?php

namespace App\Services;

use App\Repositories\ChefServiceImageRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class ChefServiceImageService
{
    public function __construct(
        protected ChefServiceImageRepository $images
    ) {
    }

    public function all(array $with = [])
    {
        return $this->images->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->images->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->images->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->images->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->images->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->images->delete($id);
    }

    public function activate($id)
    {
        return $this->images->activate($id);
    }

    public function deactivate($id)
    {
        return $this->images->deactivate($id);
    }

    /**
     * Create multiple images for a chef service
     */
    public function createMultiple(int $serviceId, array $images): Collection
    {
        $createdImages = new Collection();
        
        DB::beginTransaction();
        
        try {
            foreach ($images as $image) {
                $imageData = [
                    'chef_service_id' => $serviceId,
                    'image' => $image,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];
                
                $createdImage = $this->images->create($imageData);
                $createdImages->push($createdImage);
            }
            
            DB::commit();
            
            return $createdImages;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update images for a chef service with new images and deletions
     */
    public function updateGallery(int $serviceId, array $newImages = [], array $deleteIds = []): Collection
    {
        DB::beginTransaction();
        
        try {
            $updatedImages = new Collection();
            
            // Delete specified images
            if (!empty($deleteIds)) {
                foreach ($deleteIds as $deleteId) {
                    $this->images->delete($deleteId);
                }
            }
            
            // Add new images
            if (!empty($newImages)) {
                $createdImages = $this->createMultiple($serviceId, $newImages);
                // Convert to array and create new Collection to ensure proper type
                $updatedImages = new Collection($createdImages->all());
            }
            
            DB::commit();
            
            return $updatedImages;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a single image
     */
    public function deleteImage(int $imageId): bool
    {
        try {
            $result = $this->images->delete($imageId);
            return $result;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all images for a chef service
     */
    public function getImagesForService(int $serviceId): Collection
    {
        return $this->images->query(['service'])
            ->where('chef_service_id', $serviceId)
            ->where('is_active', true)
            ->orderBy('created_at')
            ->select(['id', 'chef_service_id', 'image', 'is_active', 'created_at']) // Only select needed columns
            ->get();
    }

    /**
     * Get first image for a chef service (for previews)
     */
    public function getFirstServiceImage(int $serviceId): ?string
    {
        $image = $this->images->query([])
            ->where('chef_service_id', $serviceId)
            ->where('is_active', true)
            ->orderBy('created_at')
            ->select(['image'])
            ->first();
            
        return $image?->image;
    }

    /**
     * Get active images count for a chef service
     */
    public function getActiveImagesCount(int $serviceId): int
    {
        return $this->images->query([])
            ->where('chef_service_id', $serviceId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Check if service has reached maximum images limit
     */
    public function hasReachedMaxImages(int $serviceId, int $maxImages = 10): bool
    {
        return $this->getActiveImagesCount($serviceId) >= $maxImages;
    }
}