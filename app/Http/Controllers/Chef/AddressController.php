<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Governorate;
use App\Models\District;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    /**
     * Display a listing of the chef's addresses.
     */
    public function index(): Response
    {
        $user = Auth::guard('chef')->user();

        $addresses = Address::where('user_id', $user->id)
            ->with(['governorate', 'district', 'area'])
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Chef/Address/Index', [
            'addresses' => $addresses->map(fn($address) => [
                'id' => $address->id,
                'label' => $address->label,
                'address' => $address->address,
                'street' => $address->street,
                'building_number' => $address->building_number,
                'floor_number' => $address->floor_number,
                'apartment_number' => $address->apartment_number,
                'lat' => $address->lat,
                'lang' => $address->lang,
                'is_default' => $address->is_default,
                'governorate' => $address->governorate?->name,
                'district' => $address->district?->name,
                'area' => $address->area?->name,
                'governorate_id' => $address->governorate_id,
                'district_id' => $address->district_id,
                'area_id' => $address->area_id,
                'created_at' => $address->created_at->format('Y-m-d H:i'),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(): Response
    {
        $locale = app()->getLocale();
        $nameField = $locale === 'ar' ? 'name_ar' : 'name_en';

        $governorates = Governorate::where('is_active', true)->get(['id', 'name_ar', 'name_en'])
            ->map(fn($g) => ['id' => $g->id, 'name' => $g->$nameField ?: $g->name_ar]);
        $districts = District::where('is_active', true)->get(['id', 'name_ar', 'name_en', 'governorate_id'])
            ->map(fn($d) => ['id' => $d->id, 'name' => $d->$nameField ?: $d->name_ar, 'governorate_id' => $d->governorate_id]);
        $areas = Area::where('is_active', true)->get(['id', 'name_ar', 'name_en', 'district_id'])
            ->map(fn($a) => ['id' => $a->id, 'name' => $a->$nameField ?: $a->name_ar, 'district_id' => $a->district_id]);

        return Inertia::render('Chef/Address/Create', [
            'governorates' => $governorates,
            'districts' => $districts,
            'areas' => $areas,
        ]);
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('chef')->user();

        $validated = $request->validate([
            'label' => 'nullable|string|max:100',
            'address' => 'required|string|max:500',
            'street' => 'required|string|max:255',
            'building_number' => 'nullable|integer',
            'floor_number' => 'nullable|integer',
            'apartment_number' => 'nullable|integer',
            'governorate_id' => 'nullable|exists:governorates,id',
            'district_id' => 'nullable|exists:districts,id',
            'area_id' => 'nullable|exists:areas,id',
            'lat' => 'nullable|numeric|between:-90,90',
            'lang' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = $user->id;

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        Address::create($validated);

        return redirect()->route('chef.addresses.index')
            ->with('success', __('address.created_successfully'));
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(Address $address): Response
    {
        $user = Auth::guard('chef')->user();

        // Ensure the address belongs to the authenticated chef
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $locale = app()->getLocale();
        $nameField = $locale === 'ar' ? 'name_ar' : 'name_en';

        $governorates = Governorate::where('is_active', true)->get(['id', 'name_ar', 'name_en'])
            ->map(fn($g) => ['id' => $g->id, 'name' => $g->$nameField ?: $g->name_ar]);
        $districts = District::where('is_active', true)->get(['id', 'name_ar', 'name_en', 'governorate_id'])
            ->map(fn($d) => ['id' => $d->id, 'name' => $d->$nameField ?: $d->name_ar, 'governorate_id' => $d->governorate_id]);
        $areas = Area::where('is_active', true)->get(['id', 'name_ar', 'name_en', 'district_id'])
            ->map(fn($a) => ['id' => $a->id, 'name' => $a->$nameField ?: $a->name_ar, 'district_id' => $a->district_id]);

        return Inertia::render('Chef/Address/Edit', [
            'address' => [
                'id' => $address->id,
                'label' => $address->label,
                'address' => $address->address,
                'street' => $address->street,
                'building_number' => $address->building_number,
                'floor_number' => $address->floor_number,
                'apartment_number' => $address->apartment_number,
                'governorate_id' => $address->governorate_id,
                'district_id' => $address->district_id,
                'area_id' => $address->area_id,
                'lat' => $address->lat,
                'lang' => $address->lang,
                'is_default' => $address->is_default,
            ],
            'governorates' => $governorates,
            'districts' => $districts,
            'areas' => $areas,
        ]);
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address)
    {
        $user = Auth::guard('chef')->user();

        // Ensure the address belongs to the authenticated chef
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:100',
            'address' => 'required|string|max:500',
            'street' => 'required|string|max:255',
            'building_number' => 'nullable|integer',
            'floor_number' => 'nullable|integer',
            'apartment_number' => 'nullable|integer',
            'governorate_id' => 'nullable|exists:governorates,id',
            'district_id' => 'nullable|exists:districts,id',
            'area_id' => 'nullable|exists:areas,id',
            'lat' => 'nullable|numeric|between:-90,90',
            'lang' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('chef.addresses.index')
            ->with('success', __('address.updated_successfully'));
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Address $address)
    {
        $user = Auth::guard('chef')->user();

        // Ensure the address belongs to the authenticated chef
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('chef.addresses.index')
            ->with('success', __('address.deleted_successfully'));
    }

    /**
     * Set an address as default.
     */
    public function setDefault(Address $address)
    {
        $user = Auth::guard('chef')->user();

        // Ensure the address belongs to the authenticated chef
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        // Unset all other defaults
        Address::where('user_id', $user->id)->update(['is_default' => false]);

        // Set this one as default
        $address->update(['is_default' => true]);

        return redirect()->route('chef.addresses.index')
            ->with('success', __('address.set_as_default'));
    }
}
