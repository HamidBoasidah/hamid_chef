<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AddressService;
use App\DTOs\AddressDTO;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;

class AddressController extends Controller
{
    use ExceptionHandler, SuccessResponse, CanFilter;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, AddressService $addressService)
    {
        $perPage = (int) $request->get('per_page', 10);
        $userId = $request->user()->id;

        $query = $addressService->getQueryForUser($userId);

        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $addresses = $query->latest()->paginate($perPage);
        $addresses->getCollection()->transform(function ($address) {
            return AddressDTO::fromModel($address)->toIndexArray();
        });

        return $this->collectionResponse($addresses, 'تم جلب قائمة العناوين بنجاح');
    }

    public function store(StoreAddressRequest $request, AddressService $addressService)
    {
        $data = $request->validated();

        $address = $addressService->create($data);

        return $this->createdResponse(
            AddressDTO::fromModel($address)->toArray(),
            'تم إنشاء العنوان بنجاح'
        );
    }

    public function show(AddressService $addressService, Request $request, $id)
    {
        try {
            $address = $addressService->findForUser($id, $request->user()->id, [
                'governorate', 'district', 'area'
            ]);
            
            $this->authorize('view', $address);
            return $this->resourceResponse(
                AddressDTO::fromModel($address)->toArray(),
                'تم جلب بيانات العنوان بنجاح'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->throwNotFoundException('العنوان المطلوب غير موجود');
        }
    }

    public function update(UpdateAddressRequest $request, AddressService $addressService, $id)
    {
        try {
            $data = $request->validated();
            $address = $addressService->findForUser($id, $request->user()->id, [
                'governorate', 'district', 'area'
            ]);
            
            $this->authorize('update', $address);
            
            $data['user_id'] = $request->user()->id;

            $updated = $addressService->updateModel($address, $data);

            return $this->updatedResponse(
                AddressDTO::fromModel($updated)->toArray(),
                'تم تحديث العنوان بنجاح'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->throwNotFoundException('العنوان المطلوب غير موجود');
        }
    }

    public function destroy(AddressService $addressService, Request $request, $id)
    {
        $address = null;
        
        try {
            $address = $addressService->findForUser($id, $request->user()->id);
            
            $this->authorize('delete', $address);

            // Check if address has related bookings
            if (method_exists($address, 'bookings') && $address->bookings()->exists()) {
                $this->throwResourceInUseException('لا يمكن حذف عنوان مرتبط بحجوزات');
            }

            $addressService->delete($id);
            return $this->deletedResponse('تم حذف العنوان بنجاح');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->throwNotFoundException('العنوان المطلوب غير موجود');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($address) {
                $this->handleDatabaseException($e, $address, [
                    'bookings' => 'حجوزات'
                ]);
            }
            throw $e;
        }
    }

    public function activate(AddressService $addressService, Request $request, $id)
    {
        try {
            $address = $addressService->findForUser($id, $request->user()->id);
            
            $this->authorize('activate', $address);

            $activated = $addressService->activate($id);
            return $this->activatedResponse(
                AddressDTO::fromModel($activated)->toArray(), 
                'تم تفعيل العنوان بنجاح'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->throwNotFoundException('العنوان المطلوب غير موجود');
        }
    }

    public function deactivate(AddressService $addressService, Request $request, $id)
    {
        try {
            $address = $addressService->findForUser($id, $request->user()->id);
            
            $this->authorize('deactivate', $address);

            $deactivated = $addressService->deactivate($id);
            return $this->deactivatedResponse(
                AddressDTO::fromModel($deactivated)->toArray(), 
                'تم تعطيل العنوان بنجاح'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->throwNotFoundException('العنوان المطلوب غير موجود');
        }
    }

    protected function getSearchableFields(): array
    {
        return [
            'label',
            'address',
            'street',
            'building_number',
            'floor_number',
            'apartment_number',
        ];
    }

    protected function getForeignKeyFilters(): array
    {
        return [
            'governorate_id' => 'governorate_id',
            'district_id' => 'district_id',
            'area_id' => 'area_id',
            'is_default' => 'is_default',
            'is_active' => 'is_active',
        ];
    }
}
