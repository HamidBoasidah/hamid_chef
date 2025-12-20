<?php

namespace App\Repositories;

use App\Models\ChefGallery;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class ChefGalleryRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef:id,user_id,name,address',
    ];

    public function __construct(ChefGallery $model)
    {
        parent::__construct($model);
    }

    /**
     * Create multiple gallery images with transaction support
     */
    public function createMultiple(array $imagesData): Collection
    {
        $createdImages = collect();
        
        DB::beginTransaction();
        
        try {
            foreach ($imagesData as $imageData) {
                $createdImage = $this->create($imageData);
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
     * Delete multiple images by IDs with transaction support
     */
    public function deleteMultiple(array $ids): bool
    {
        DB::beginTransaction();
        
        try {
            foreach ($ids as $id) {
                $this->delete($id);
            }
            
            DB::commit();
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get gallery images for a specific chef
     */
    public function getByChefId(int $chefId, bool $activeOnly = true): Collection
    {
        $query = $this->makeQuery()
            ->where('chef_id', $chefId);
            
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('sort_order')->get();
    }

    /**
     * Update sort order for multiple images
     */
    public function updateSortOrder(array $imageOrders): bool
    {
        DB::beginTransaction();
        
        try {
            foreach ($imageOrders as $imageId => $sortOrder) {
                $this->makeQuery([])
                    ->where('id', $imageId)
                    ->update([
                        'sort_order' => $sortOrder,
                        'updated_by' => auth()->id(),
                        'updated_at' => now()
                    ]);
            }
            
            DB::commit();
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get maximum sort order for a chef
     */
    public function getMaxSortOrder(int $chefId): int
    {
        return $this->makeQuery([])
            ->where('chef_id', $chefId)
            ->max('sort_order') ?? 0;
    }

    /**
     * Count active images for a chef
     */
    public function countActiveByChef(int $chefId): int
    {
        return $this->makeQuery([])
            ->where('chef_id', $chefId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Batch update gallery images for a chef
     */
    public function batchUpdateForChef(int $chefId, array $newImages, array $deleteIds = []): Collection
    {
        DB::beginTransaction();
        
        try {
            $results = collect();
            
            // Delete specified images
            if (!empty($deleteIds)) {
                $this->deleteMultiple($deleteIds);
            }
            
            // Create new images
            if (!empty($newImages)) {
                $maxOrder = $this->getMaxSortOrder($chefId);
                
                foreach ($newImages as $index => $imageData) {
                    $imageData['chef_id'] = $chefId;
                    $imageData['sort_order'] = $maxOrder + $index + 1;
                    $imageData['is_active'] = true;
                    $imageData['created_by'] = auth()->id();
                    $imageData['updated_by'] = auth()->id();
                    
                    $createdImage = $this->create($imageData);
                    $results->push($createdImage);
                }
            }
            
            DB::commit();
            return $results;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
