<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\KycDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKycRequest;
use App\Http\Requests\UpdateKycRequest;
use App\Models\Kyc;
use App\Models\User;
use App\Services\KycService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KycController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kycs.view')->only(['index', 'show', 'viewDocument', 'downloadDocument']);
        $this->middleware('permission:kycs.create')->only(['create', 'store']);
        $this->middleware('permission:kycs.update')->only(['edit', 'update']);
        $this->middleware('permission:kycs.delete')->only(['destroy']);
    }

    public function index(Request $request, KycService $kycService): Response
    {
        $perPage = (int) $request->input('per_page', 10);

        $kycs = $kycService->paginate($perPage);

        $kycs->getCollection()->transform(function ($kyc) {
            $dto = KycDTO::fromModel($kyc)->toArray();
            /*$dto['user'] = $kyc->user
                ? $kyc->user->only(['id', 'first_name', 'last_name', 'email', 'phone_number', 'avatar'])
                : null;*/

            return $dto;
        });

        return Inertia::render('Admin/Kyc/Index', [
            'kycs' => $kycs,
        ]);
    }

    public function create(): Response
    {
        // need users for selection
        $users = User::all(['id', 'first_name', 'last_name', 'email', 'phone_number']);

        return Inertia::render('Admin/Kyc/Create', [
            'users' => $users,
        ]);
    }

    public function store(StoreKycRequest $request, KycService $kycService): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('document_scan_copy')) {
            $data['document_scan_copy'] = $request->file('document_scan_copy');
        }

        $kycService->create($data);

        return redirect()->route('admin.kycs.index');
    }

    public function show(Kyc $kyc): Response
    {
        $dto = KycDTO::fromModel($kyc)->toArray();
        /*$dto['user'] = $kyc->user
            ? $kyc->user->only(['id', 'first_name', 'last_name', 'email', 'phone_number', 'avatar'])
            : null;*/

        return Inertia::render('Admin/Kyc/Show', [
            'kyc' => $dto,
        ]);
    }

    public function edit(Kyc $kyc): Response
    {
        $dto = KycDTO::fromModel($kyc)->toArray();
        /*$dto['user'] = $kyc->user
            ? $kyc->user->only(['id', 'first_name', 'last_name', 'email', 'phone_number', 'avatar'])
            : null;*/

        // need users for selection
        $users = User::all(['id', 'first_name', 'last_name', 'email', 'phone_number']);

        return Inertia::render('Admin/Kyc/Edit', [
            'kyc' => $dto,
            'users' => $users,
        ]);
    }

    public function update(UpdateKycRequest $request, KycService $kycService, Kyc $kyc): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('document_scan_copy')) {
            $data['document_scan_copy'] = $request->file('document_scan_copy');
        }

        $kycService->update($kyc->id, $data);

        return redirect()->route('admin.kycs.index');
    }

    public function destroy(KycService $kycService, Kyc $kyc): RedirectResponse
    {
        $kycService->delete($kyc->id);

        return redirect()->route('admin.kycs.index');
    }

    public function viewDocument(Kyc $kyc, KycService $kycService): StreamedResponse
    {
        return $kycService->streamDocument($kyc, false);
    }

    public function downloadDocument(Kyc $kyc, KycService $kycService): StreamedResponse
    {
        return $kycService->streamDocument($kyc, true);
    }

    // NOTE: user selection is provided inline in create/edit methods to follow the
    // same pattern used elsewhere (e.g. governorates for selection). This avoids
    // having a shared helper method and keeps the logic local to the controller
    // action. If you prefer a reusable method, we can restore it.
}
