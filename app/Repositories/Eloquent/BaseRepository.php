<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    /**
     * العلاقات التي تُحمَّل تلقائيًا مع كل استعلام على هذا الـ Repository
     */
    protected array $defaultWith = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * يبني Query مع تحكم كامل في العلاقات:
     * - null  => استخدم defaultWith
     * - []    => بدون علاقات
     * - array => استعمل هذه العلاقات فقط
     */
    /*
    استعلام خفيف بدون أي علاقات (مثلاً count):
    $count = $this->addresses->query([])->count();
    // هنا [] تعني "لا تستخدِم defaultWith"
    */
    /*
    استعلام بعلاقات مختلفة عن الافتراضية:
    $addresses = $this->addresses->paginate(10, ['governorate', 'district']);
    // هنا ستُستخدم العلاقات هذه فقط، بدون defaultWith (لأننا حدّدناها يدويًا)
    */


    protected function makeQuery(?array $with = null): Builder
    {
        if ($with === null) {
            // استخدم العلاقات الافتراضية
            $relations = $this->defaultWith;
        } else {
            // المستخدم يحدد بالضبط ما يريد (أو لا شيء)
            $relations = $with;
        }
    
        $query = $this->model->newQuery();
    
        if (! empty($relations)) {
            $query->with($relations);
        }
    
        return $query;
    }

    /**
     * إن احتجت Query خام (تستخدمه في أماكن أخرى)
     */
    public function query(array $with = []): Builder
    {
        return $this->makeQuery($with);
    }

    public function all(array $with = [])
    {
        return $this->makeQuery($with)->latest()->get();
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->makeQuery($with)->latest()->paginate($perPage);
    }

    public function find(int|string $id, array $with = [])
    {
        return $this->makeQuery($with)->find($id);
    }

    public function findOrFail(int|string $id, array $with = [])
    {
        return $this->makeQuery($with)->findOrFail($id);
    }

    /**
     * سجلات خاصة بمستخدم معيّن (يعتمد على وجود user_id)
     */
    public function forUser(int $userId, array $with = []): Builder
    {
        return $this->makeQuery($with)->where('user_id', $userId);
    }

    /**
     * جلب سجل واحد يخص مستخدم معيّن أو يرمي ModelNotFoundException
     */
    public function findForUser(int|string $id, int $userId, array $with = [])
    {
        return $this->forUser($userId, $with)->findOrFail($id);
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

    /**
     * مفيد للـ API لما يكون الـ Model جاهز عندك
     */
    public function updateModel(Model $record, array $attributes)
    {
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

    // handleFileUploads + storePrivateFile + deletePrivateFile كما هي
    protected function handleFileUploads(array $attributes, ?Model $record = null): array
    {
        foreach ($attributes as $key => &$value) {
            if ($value instanceof UploadedFile) {
                if ($record && $record->{$key} && Storage::disk('public')->exists($record->{$key})) {
                    Storage::disk('public')->delete($record->{$key});
                }

                $filename = (string) Str::uuid() . '.' . $value->getClientOriginalExtension();
                $path = $value->storeAs($this->model->getTable(), $filename, 'public');

                $value = $path;
            }
        }

        return $attributes;
    }

    protected function storePrivateFile(UploadedFile $file, ?string $oldPath = null, string $directory = 'private'): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        $disk = Storage::disk('local');

        if ($oldPath && $disk->exists($oldPath)) {
            $disk->delete($oldPath);
        }

        $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
        $fullPath = trim($directory, '/');
        $storedPath = $file->storeAs($fullPath, $filename, 'local');

        return $storedPath;
    }

    protected function deletePrivateFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $disk = Storage::disk('local');

        if ($disk->exists($path)) {
            return $disk->delete($path);
        }

        return false;
    }
}
