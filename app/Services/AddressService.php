<?php

namespace App\Services;

use App\Repositories\AddressRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AddressService
{
    protected AddressRepository $addresses;

    public function __construct(AddressRepository $addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * Query عام (لو احتجته في حالات خاصة)
     */
    public function query(?array $with = null): Builder
    {
        return $this->addresses->query($with);
    }

    /**
     * تستخدم في لوحة التحكم أو أي مكان عام
     * - $with = null  => يستعمل defaultWith في AddressRepository
     * - $with = []    => بدون علاقات
     * - $with = ['..']=> علاقات مخصصة
     */
    public function all(?array $with = null)
    {
        return $this->addresses->all($with);
    }

    public function paginate(int $perPage = 15, ?array $with = null)
    {
        return $this->addresses->paginate($perPage, $with);
    }

    public function find(int|string $id, ?array $with = null)
    {
        return $this->addresses->findOrFail($id, $with);
    }

    /**
     * إنشاء عنوان جديد
     * - في الـ API: يربط العنوان بالمستخدم الحالي تلقائيًا إذا لم يُرسل user_id
     * - في لوحة التحكم: يمكن تمرير user_id من الفورم
     */
    public function create(array $attributes)
    {
        if (empty($attributes['user_id']) && Auth::check()) {
            $attributes['user_id'] = Auth::id();
        }

        return $this->addresses->create($attributes);
    }

    /**
     * تحديث بالـ id (مناسب للـ Admin)
     */
    public function update(int|string $id, array $attributes)
    {
        return $this->addresses->update($id, $attributes);
    }

    /**
     * تحديث Model جاهز (مناسب للـ API بعد findForUser + Policy)
     */
    public function updateModel(Model $address, array $attributes)
    {
        return $this->addresses->updateModel($address, $attributes);
    }

    public function delete(int|string $id): bool
    {
        return $this->addresses->delete($id);
    }

    public function activate(int|string $id)
    {
        return $this->addresses->activate($id);
    }

    public function deactivate(int|string $id)
    {
        return $this->addresses->deactivate($id);
    }

    /**
     * 🔹 API: Query لعناوين مستخدم معيّن (index مع فلاتر)
     * - يرجع Builder عشان تقدر تطبق CanFilter و باقي الفلاتر
     * - يستفيد من defaultWith في AddressRepository لما $with = null
     */
    public function getQueryForUser(int $userId, ?array $with = null): Builder
    {
        return $this->addresses->forUser($userId, $with);
    }

    /**
     * (اختياري) لو حبيت تستعملها مباشرة بدون فلاتر إضافية
     */
    public function allForUser(int $userId, ?array $with = null)
    {
        return $this->addresses->allForUser($userId, $with);
    }

    public function paginateForUser(int $userId, int $perPage = 15, ?array $with = null)
    {
        return $this->addresses->paginateForUser($userId, $perPage, $with);
    }

    /**
     * 🔹 API: جلب عنوان مملوك لمستخدم معيّن (show / update / delete / activate / deactivate)
     */
    public function findForUser(int|string $id, int $userId, ?array $with = null)
    {
        return $this->addresses->findForUser($id, $userId, $with);
    }
}
