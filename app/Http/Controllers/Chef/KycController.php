<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Services\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class KycController extends Controller
{
    protected KycService $kycService;

    public function __construct(KycService $kycService)
    {
        $this->kycService = $kycService;
    }

    /**
     * Display a listing of the chef's KYC requests.
     */
    public function index(): Response
    {
        $user = Auth::guard('chef')->user();

        $kycs = Kyc::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Chef/Kyc/Index', [
            'kycs' => $kycs->map(fn($kyc) => [
                'id' => $kyc->id,
                'document_type' => $kyc->document_type,
                'status' => $kyc->status,
                'full_name' => $kyc->full_name,
                'gender' => $kyc->gender,
                'date_of_birth' => $kyc->date_of_birth?->format('Y-m-d'),
                'address' => $kyc->address,
                'document_scan_copy' => $kyc->document_scan_copy,
                'certificates' => $kyc->certificates,
                'rejected_reason' => $kyc->rejected_reason,
                'verified_at' => $kyc->verified_at?->format('Y-m-d H:i'),
                'created_at' => $kyc->created_at->format('Y-m-d H:i'),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new KYC request.
     */
    public function create(): Response
    {
        return Inertia::render('Chef/Kyc/Create', [
            'documentTypes' => [
                ['value' => 'id_card', 'label' => __('kyc.types.id_card')],
                ['value' => 'passport', 'label' => __('kyc.types.passport')],
                ['value' => 'driving_license', 'label' => __('kyc.types.driving_license')],
            ],
        ]);
    }

    /**
     * Store a newly created KYC request.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('chef')->user();

        $validated = $request->validate([
            'document_type' => 'required|in:id_card,passport,driving_license',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'document_scan_copy' => 'required|image|max:5120',
            'certificates' => 'nullable|array',
            'certificates.*' => 'image|max:5120',
        ]);

        // Handle document scan upload
        $validated['document_scan_copy'] = $request->file('document_scan_copy')
            ->store('kyc/documents', 'public');

        // Handle certificates upload
        if ($request->hasFile('certificates')) {
            $certificatePaths = [];
            foreach ($request->file('certificates') as $certificate) {
                $certificatePaths[] = $certificate->store('kyc/certificates', 'public');
            }
            $validated['certificates'] = $certificatePaths;
        }

        $validated['user_id'] = $user->id;
        $validated['status'] = 'pending';

        Kyc::create($validated);

        return redirect()->route('chef.kyc.index')
            ->with('success', __('kyc.created_successfully'));
    }

    /**
     * Display the specified KYC request.
     */
    public function show(Kyc $kyc): Response
    {
        $user = Auth::guard('chef')->user();

        // Ensure the KYC belongs to the authenticated chef
        if ($kyc->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Chef/Kyc/Show', [
            'kyc' => [
                'id' => $kyc->id,
                'document_type' => $kyc->document_type,
                'status' => $kyc->status,
                'full_name' => $kyc->full_name,
                'gender' => $kyc->gender,
                'date_of_birth' => $kyc->date_of_birth?->format('Y-m-d'),
                'address' => $kyc->address,
                'document_scan_copy' => $kyc->document_scan_copy,
                'certificates' => $kyc->certificates,
                'rejected_reason' => $kyc->rejected_reason,
                'verified_at' => $kyc->verified_at?->format('Y-m-d H:i'),
                'created_at' => $kyc->created_at->format('Y-m-d H:i'),
                'updated_at' => $kyc->updated_at->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified KYC request.
     */
    public function edit(Kyc $kyc): Response|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::guard('chef')->user();

        // Ensure the KYC belongs to the authenticated chef
        if ($kyc->user_id !== $user->id) {
            abort(403);
        }

        // Only allow editing if status is pending or rejected
        if (!in_array($kyc->status, ['pending', 'rejected'])) {
            return redirect()->route('chef.kyc.index')
                ->with('error', __('kyc.cannot_edit_approved'));
        }

        return Inertia::render('Chef/Kyc/Edit', [
            'kyc' => [
                'id' => $kyc->id,
                'document_type' => $kyc->document_type,
                'status' => $kyc->status,
                'full_name' => $kyc->full_name,
                'gender' => $kyc->gender,
                'date_of_birth' => $kyc->date_of_birth?->format('Y-m-d'),
                'address' => $kyc->address,
                'document_scan_copy' => $kyc->document_scan_copy,
                'certificates' => $kyc->certificates,
            ],
            'documentTypes' => [
                ['value' => 'id_card', 'label' => __('kyc.types.id_card')],
                ['value' => 'passport', 'label' => __('kyc.types.passport')],
                ['value' => 'driving_license', 'label' => __('kyc.types.driving_license')],
            ],
        ]);
    }

    /**
     * Update the specified KYC request.
     */
    public function update(Request $request, Kyc $kyc)
    {
        $user = Auth::guard('chef')->user();

        // Ensure the KYC belongs to the authenticated chef
        if ($kyc->user_id !== $user->id) {
            abort(403);
        }

        // Only allow updating if status is pending or rejected
        if (!in_array($kyc->status, ['pending', 'rejected'])) {
            return redirect()->route('chef.kyc.index')
                ->with('error', __('kyc.cannot_edit_approved'));
        }

        $validated = $request->validate([
            'document_type' => 'required|in:id_card,passport,driving_license',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'document_scan_copy' => 'nullable|image|max:5120',
            'certificates' => 'nullable|array',
            'certificates.*' => 'image|max:5120',
        ]);

        // Handle document scan upload
        if ($request->hasFile('document_scan_copy')) {
            // Delete old file
            if ($kyc->document_scan_copy) {
                Storage::disk('public')->delete($kyc->document_scan_copy);
            }
            $validated['document_scan_copy'] = $request->file('document_scan_copy')
                ->store('kyc/documents', 'public');
        }

        // Handle certificates upload
        if ($request->hasFile('certificates')) {
            // Delete old certificates
            if ($kyc->certificates) {
                foreach ($kyc->certificates as $cert) {
                    Storage::disk('public')->delete($cert);
                }
            }
            $certificatePaths = [];
            foreach ($request->file('certificates') as $certificate) {
                $certificatePaths[] = $certificate->store('kyc/certificates', 'public');
            }
            $validated['certificates'] = $certificatePaths;
        }

        // Reset status to pending when updating
        $validated['status'] = 'pending';
        $validated['rejected_reason'] = null;

        $kyc->update($validated);

        return redirect()->route('chef.kyc.index')
            ->with('success', __('kyc.updated_successfully'));
    }

    /**
     * Remove the specified KYC request.
     */
    public function destroy(Kyc $kyc)
    {
        $user = Auth::guard('chef')->user();

        // Ensure the KYC belongs to the authenticated chef
        if ($kyc->user_id !== $user->id) {
            abort(403);
        }

        // Only allow deleting if status is pending or rejected
        if (!in_array($kyc->status, ['pending', 'rejected'])) {
            return redirect()->route('chef.kyc.index')
                ->with('error', __('kyc.cannot_delete_approved'));
        }

        // Delete associated files
        if ($kyc->document_scan_copy) {
            Storage::disk('public')->delete($kyc->document_scan_copy);
        }
        if ($kyc->certificates) {
            foreach ($kyc->certificates as $cert) {
                Storage::disk('public')->delete($cert);
            }
        }

        $kyc->delete();

        return redirect()->route('chef.kyc.index')
            ->with('success', __('kyc.deleted_successfully'));
    }

    /**
     * Download a KYC document.
     */
    public function download(Kyc $kyc, string $type)
    {
        $user = Auth::guard('chef')->user();

        // Ensure the KYC belongs to the authenticated chef
        if ($kyc->user_id !== $user->id) {
            abort(403);
        }

        $file = match($type) {
            'document' => $kyc->document_scan_copy,
            default => null,
        };

        if (!$file || !Storage::disk('public')->exists($file)) {
            abort(404);
        }

        return Storage::disk('public')->download($file);
    }
}
