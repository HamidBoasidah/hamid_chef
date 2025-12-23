<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\BookingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:bookings.view')->only(['index', 'show']);
        $this->middleware('permission:bookings.create')->only(['create', 'store']);
        $this->middleware('permission:bookings.update')->only(['edit', 'update']);
        $this->middleware('permission:bookings.delete')->only(['destroy']);
    }

    /**
     * Display a listing of bookings
     */
    public function index(Request $request, BookingService $bookingService)
    {
        try {
            $perPage = $request->get('per_page', 9);
            $with = ['customer', 'chef', 'service', 'address'];
            
            $bookings = $bookingService->paginate($perPage, $with);
            
            return Inertia::render('Admin/Bookings/Index', [
                'bookings' => $bookings,
                'filters' => $request->only(['search', 'status', 'chef_id', 'date_from', 'date_to'])
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error retrieving bookings: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new booking
     */
    public function create()
    {
        $customers = \App\Models\User::where('user_type', 'customer')->get(['id', 'first_name', 'last_name', 'email']);
        $chefs = \App\Models\Chef::where('is_active', true)->get(['id', 'name', 'email']);
        $addresses = \App\Models\Address::where('is_active', true)->with('user:id,first_name,last_name')->get(['id', 'user_id', 'address', 'label']);
        $chefServices = \App\Models\ChefService::with('chef')->where('is_active', true)->get([
            'id', 'chef_id', 'name', 'service_type', 'hourly_rate', 'package_price', 
            'min_hours', 'max_guests_included', 'allow_extra_guests', 'extra_guest_price'
        ]);

        return Inertia::render('Admin/Bookings/Create', [
            'customers' => $customers,
            'chefs' => $chefs,
            'addresses' => $addresses,
            'chefServices' => $chefServices
        ]);
    }

    /**
     * Store a newly created booking
     */
    public function store(StoreBookingRequest $request, BookingService $bookingService)
    {
        try {
            $validated = $request->validated();
            $validated['created_by'] = Auth::id();

            // Create BookingDTO
            $bookingDTO = new BookingDTO(
                null,
                $validated['customer_id'],
                $validated['chef_id'],
                $validated['chef_service_id'],
                $validated['address_id'] ?? null,
                $validated['date'],
                $validated['start_time'],
                $validated['hours_count'],
                $validated['number_of_guests'],
                $validated['service_type'],
                $validated['unit_price'],
                $validated['extra_guests_count'] ?? 0,
                $validated['extra_guests_amount'] ?? 0,
                $validated['total_amount'],
                $validated['commission_amount'] ?? 0,
                $validated['payment_status'] ?? 'pending',
                $validated['booking_status'] ?? 'pending',
                $validated['notes'] ?? null,
                $validated['is_active'] ?? true,
                $validated['created_by'],
                null
            );

            $result = $bookingService->createWithConflictCheck($bookingDTO);

            if (!$result['success']) {
                return back()->withErrors([
                    'conflict' => $result['message'],
                    'conflicting_bookings' => $result['conflicting_bookings'] ?? []
                ])->withInput();
            }

            // Admin created booking successfully (logging removed)

            return redirect()->route('admin.bookings.show', $result['booking']->id)
                           ->with('success', 'Booking created successfully');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error creating booking: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified booking
     */
    public function show(int $id, BookingService $bookingService)
    {
        try {
            $booking = $bookingService->find($id, ['customer', 'chef', 'service', 'address', 'transactions', 'rating']);
            
            return Inertia::render('Admin/Bookings/Show', [
                'booking' => BookingDTO::fromModel($booking)->toArray()
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Booking not found: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified booking
     */
    public function edit(int $id, BookingService $bookingService)
    {
        try {
            $booking = $bookingService->find($id, ['customer', 'chef', 'service', 'address']);
            
            $customers = \App\Models\User::where('user_type', 'customer')->get(['id', 'first_name', 'last_name', 'email']);
            $chefs = \App\Models\Chef::where('is_active', true)->get(['id', 'name', 'email']);
            $addresses = \App\Models\Address::where('is_active', true)->get(['id', 'address']);
            
            return Inertia::render('Admin/Bookings/Edit', [
                'booking' => BookingDTO::fromModel($booking)->toArray(),
                'customers' => $customers,
                'chefs' => $chefs,
                'addresses' => $addresses
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Booking not found: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified booking
     */
    public function update(UpdateBookingRequest $request, int $id, BookingService $bookingService)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = Auth::id();

            $booking = $bookingService->update($id, $validated);

            // Admin updated booking successfully (logging removed)

            return redirect()->route('admin.bookings.show', $id)
                           ->with('success', 'Booking updated successfully');

        } catch (\Exception $e) {
            $errorMessage = $e->getCode() === 409 ? 
                'Booking update conflicts with existing reservations: ' . $e->getMessage() :
                'Error updating booking: ' . $e->getMessage();
            
            return back()->withErrors(['error' => $errorMessage])
                        ->withInput();
        }
    }

    /**
     * Cancel the specified booking
     */
    public function destroy(int $id, BookingService $bookingService)
    {
        try {
            $cancelled = $bookingService->cancel($id, 'cancelled_by_admin');

            if (!$cancelled) {
                return back()->withErrors(['error' => 'Failed to cancel booking']);
            }

            // Admin cancelled booking successfully (logging removed)

            return redirect()->route('admin.bookings.index')
                           ->with('success', 'Booking cancelled successfully');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error cancelling booking: ' . $e->getMessage()]);
        }
    }

    /**
     * Check chef availability (AJAX endpoint for admin)
     */
    public function checkAvailability(Request $request, BookingService $bookingService)
    {
        $request->validate([
            'chef_id' => 'required|integer|exists:chefs,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'hours_count' => 'required|integer|min:1|max:12',
            'exclude_booking_id' => 'nullable|integer|exists:bookings,id'
        ]);

        try {
            $availability = $bookingService->checkAvailability(
                $request->chef_id,
                $request->date,
                $request->start_time,
                $request->hours_count,
                $request->exclude_booking_id
            );

            return response()->json([
                'success' => true,
                'data' => $availability,
                'message' => $availability['available'] ? 'Time slot is available' : 'Time slot is not available'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking availability',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get chef bookings for calendar view
     */
    public function getChefBookings(Request $request, BookingService $bookingService)
    {
        $request->validate([
            'chef_id' => 'required|integer|exists:chefs,id',
            'date' => 'required|date'
        ]);

        try {
            $bookings = $bookingService->getChefBookingsForDate($request->chef_id, $request->date);

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'Chef bookings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving chef bookings',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Bulk update booking statuses
     */
    public function bulkUpdate(Request $request, BookingService $bookingService)
    {
        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'integer|exists:bookings,id',
            'action' => 'required|in:accept,reject,cancel',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $updated = 0;
            $errors = [];

            foreach ($request->booking_ids as $bookingId) {
                try {
                    switch ($request->action) {
                        case 'accept':
                            $bookingService->update($bookingId, [
                                'booking_status' => 'accepted',
                                'updated_by' => Auth::id()
                            ]);
                            break;
                        case 'reject':
                            $bookingService->update($bookingId, [
                                'booking_status' => 'rejected',
                                'notes' => $request->reason,
                                'updated_by' => Auth::id()
                            ]);
                            break;
                        case 'cancel':
                            $bookingService->cancel($bookingId, 'cancelled_by_admin');
                            break;
                    }
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Booking ID {$bookingId}: " . $e->getMessage();
                }
            }

            // Admin bulk updated bookings (logging removed)

            $message = "Successfully updated {$updated} booking(s)";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating bookings: ' . $e->getMessage()]);
        }
    }
}