<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function query()
    {
        return $this->model->newQuery();
    }

    public function all(array $with = [])
    {
        return $this->query()->with($with)->latest()->get();
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->query()->with($with)->latest()->paginate($perPage);
    }

    public function find(int|string $id, array $with = [])
    {
        return $this->query()->with($with)->find($id);
    }

    public function findOrFail(int|string $id, array $with = [])
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    public function create(array $attributes)
    {
        $attributes = $this->handleFileUploads($attributes);
        return $this->model->create($attributes);
    }

    public function update(int|string $id, array $attributes)
    {
        $record = $this->findOrFail($id);
        $attributes = $this->handleFileUploads($attributes, $record);
        $record->update($attributes);
        return $record;
    }

    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);
        return (bool) $record->delete();
    }

    public function activate(int|string $id)
    {
        $record = $this->findOrFail($id);
        $record->update(['is_active' => true]);
        return $record;
    }

    public function deactivate(int|string $id)
    {
        $record = $this->findOrFail($id);
        $record->update(['is_active' => false]);
        return $record;
    }

    /**
     * يعالج رفع الملفات ويستبدل كائن الملف بالمسار.
     *
     * @param array $attributes
     * @param Model|null $record السجل الحالي (يستخدم في التحديث لحذف الملف القديم)
     * @return array
     */
    protected function handleFileUploads(array $attributes, ?Model $record = null): array
    {
        foreach ($attributes as $key => &$value) {
            if ($value instanceof UploadedFile) {
                // في حالة التحديث، احذف الملف القديم إذا كان موجودًا
                if ($record && $record->{$key} && Storage::disk('public')->exists($record->{$key})) {
                    Storage::disk('public')->delete($record->{$key});
                }

                // قم بتخزين الملف الجديد في مجلد يعتمد على اسم جدول النموذج
                $path = $value->store($this->model->getTable(), 'public');
                $value = $path; // استبدل كائن الملف بالمسار
            }
        }

        return $attributes;
    }
}
