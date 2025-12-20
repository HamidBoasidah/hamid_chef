<?php

namespace App\Services;

use App\Repositories\ChefGalleryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

use Exception;

class ChefGalleryService
{
    public function __construct(
        protected ChefGalleryRepository $galleries
    ) {
    }

    public function all(array $with = [])
    {
        return $this->galleries->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->galleries->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->galleries->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->galleries->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->galleries->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->galleries->delete($id);
    }

    public function activate($id)
    {
        return $this->galleries->activate($id);
    }

    public function deactivate($id)
    {
        return $this->galleries->deactivate($id);
    }

    /**
     * Create multiple gallery images for a chef
     */
    public function createMultiple(int $chefId, array $images): Collection
    {
        $createdImages = new Collection();
        
        DB::beginTransaction();
        
        try {
            foreach ($images as $image) {
                $imageData = [
                    'chef_id' => $chefId,
                    'image' => $image,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];
                
                $createdImage = $this->galleries->create($imageData);
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
     * Update gallery for a chef with new images and deletions
     */
    public function updateGallery(int $chefId, array $newImages = [], array $deleteIds = []): Collection
    {
        DB::beginTransaction();
        
        try {
            $updatedImages = new Collection();
            
            // Delete specified images
            if (!empty($deleteIds)) {
                foreach ($deleteIds as $deleteId) {
                    $this->galleries->delete($deleteId);
                }
            }
            
            // Add new images
            if (!empty($newImages)) {
                $createdImages = $this->createMultiple($chefId, $newImages);
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
            $result = $this->galleries->delete($imageId);
            

            
            return $result;
            
        } catch (Exception $e) {

            
            throw $e;
        }
    }

    /**
     * Get all gallery images for a chef
     */
    public function getGalleryForChef(int $chefId): Collection
    {
        return $this->galleries->query(['chef'])
            ->where('chef_id', $chefId)
            ->where('is_active', true)
            ->orderBy('created_at')
            ->select(['id', 'chef_id', 'image', 'is_active', 'created_at']) // Only select needed columns
            ->get();
    }

    /**
     * Get first gallery image for a chef (for previews)
     */
    public function getFirstGalleryImage(int $chefId): ?string
    {
        $image = $this->galleries->query([])
            ->where('chef_id', $chefId)
            ->where('is_active', true)
            ->orderBy('created_at')
            ->select(['image'])
            ->first();
            
        return $image?->image;
    }



    /**
     * Get active gallery images count for a chef
     */
    public function getActiveImagesCount(int $chefId): int
    {
        return $this->galleries->query([])
            ->where('chef_id', $chefId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Check if chef has reached maximum images limit
     */
    public function hasReachedMaxImages(int $chefId, int $maxImages = 10): bool
    {
        return $this->getActiveImagesCount($chefId) >= $maxImages;
    }
}
