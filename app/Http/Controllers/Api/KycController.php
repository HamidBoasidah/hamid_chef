<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KycService;
use App\DTOs\KycDTO;
use App\Http\Requests\StoreKycRequest;
use App\Http\Requests\UpdateKycRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use App\Exceptions\ValidationException as AppValidationException;

class KycController extends Controller
{
    use ExceptionHandler, SuccessResponse, CanFilter;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, KycService $kycService)
    {
        $perPage = (int) $request->get('per_page', 10);
        $userId = $request->user()->id;

        $query = $kycService->getQueryForUser($userId);

        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $kycs = $query->latest()->paginate($perPage);

        $kycs->getCollection()->transform(function ($kyc) {
            return KycDTO::fromModel($kyc)->toIndexArray();
        });

        return $this->collectionResponse($kycs, 'تم جلب قائمة طلبات KYC بنجاح');
    }

    public function store(StoreKycRequest $request, KycService $kycService)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('document_scan_copy')) {
                $data['document_scan_copy'] = $request->file('document_scan_copy');
            }

            // Always associate with authenticated user
            $data['user_id'] = $request->user()->id;

            // Ensure API users cannot set the `status` field (admin-only)
            if (isset($data['status'])) {
                unset($data['status']);
            }

            $kyc = $kycService->create($data);

            return $this->createdResponse(KycDTO::fromModel($kyc)->toArray(), 'تم إنشاء طلب KYC بنجاح');
        } catch (AppValidationException $e) {
            // Our App\Exceptions\ValidationException has a render() that returns the
            // standardized JSON structure. Reuse it so API clients get consistent errors.
            return $e->render($request);
        }
    }

    public function show(Request $request, KycService $kycService, $id)
    {
        try {
            $kyc = $kycService->findForUser($id, $request->user()->id);

            $this->authorize('view', $kyc);

            return $this->resourceResponse(KycDTO::fromModel($kyc)->toArray(), 'تم جلب بيانات الطلب');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('طلب KYC غير موجود');
        }
    }

    public function update(UpdateKycRequest $request, KycService $kycService, $id)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('document_scan_copy')) {
                $data['document_scan_copy'] = $request->file('document_scan_copy');
            }

            $kyc = $kycService->findForUser($id, $request->user()->id);

            $this->authorize('update', $kyc);


            // Ensure ownership remains
            $data['user_id'] = $request->user()->id;

            // Ensure API users cannot set the `status` field (admin-only)
            if (isset($data['status'])) {
                unset($data['status']);
            }

            // Update the already-loaded model (parity with AddressService)
            $updated = $kycService->updateModel($kyc, $data);

            return $this->updatedResponse(KycDTO::fromModel($updated)->toArray(), 'تم تحديث طلب KYC بنجاح');
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('طلب KYC غير موجود');
        }
    }

    public function destroy(Request $request, KycService $kycService, $id)
    {
        try {
            $kyc = $kycService->findForUser($id, $request->user()->id);

            $this->authorize('delete', $kyc);

            $kycService->delete($kyc->id);

            return $this->deletedResponse('تم حذف طلب KYC بنجاح');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('طلب KYC غير موجود');
        }
    }

    public function viewDocument(Request $request, KycService $kycService, $id)
    {
        try {
            $kyc = $kycService->findForUser($id, $request->user()->id);

            $this->authorize('view', $kyc);

            return $kycService->streamDocument($kyc, false);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('طلب KYC غير موجود');
        }
    }

    public function downloadDocument(Request $request, KycService $kycService, $id)
    {
        try {
            $kyc = $kycService->findForUser($id, $request->user()->id);

            $this->authorize('view', $kyc);

            return $kycService->streamDocument($kyc, true);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('طلب KYC غير موجود');
        }
    }

    protected function getSearchableFields(): array
    {
        return [
            'full_name',
            'address',
            'document_type',
        ];
    }

    protected function getForeignKeyFilters(): array
    {
        return [
            'status' => 'status',
            'is_verified' => 'is_verified',
        ];
    }
}
