<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|integer|exists:users,id',
            'chef_id' => 'required|integer|exists:chefs,id',
            'chef_service_id' => 'required|integer|exists:chef_services,id',
            'address_id' => 'nullable|integer|exists:addresses,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'hours_count' => 'required|integer|min:1|max:12',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'service_type' => 'required|in:hourly,package',
            'unit_price' => 'required|numeric|min:0|max:9999999.99',
            'extra_guests_count' => 'nullable|integer|min:0|max:20',
            'extra_guests_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'total_amount' => 'required|numeric|min:0|max:9999999.99',
            'commission_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'payment_status' => 'nullable|in:pending,paid,refunded,failed',
            'booking_status' => 'nullable|in:pending,accepted,rejected,cancelled_by_customer,cancelled_by_chef,completed',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean'
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // English messages
            'chef_id.required' => __('booking.validation.chef_required'),
            'chef_id.exists' => __('booking.validation.chef_not_found'),
            'chef_service_id.required' => __('booking.validation.service_required'),
            'chef_service_id.exists' => __('booking.validation.service_not_found'),
            'date.required' => __('booking.validation.date_required'),
            'date.after_or_equal' => __('booking.validation.date_future'),
            'start_time.required' => __('booking.validation.start_time_required'),
            'start_time.date_format' => __('booking.validation.start_time_format'),
            'hours_count.required' => __('booking.validation.hours_required'),
            'hours_count.min' => __('booking.validation.hours_min'),
            'hours_count.max' => __('booking.validation.hours_max'),
            'number_of_guests.required' => __('booking.validation.guests_required'),
            'number_of_guests.min' => __('booking.validation.guests_min'),
            'number_of_guests.max' => __('booking.validation.guests_max'),
            'service_type.required' => __('booking.validation.service_type_required'),
            'service_type.in' => __('booking.validation.service_type_invalid'),
            'unit_price.required' => __('booking.validation.price_required'),
            'unit_price.numeric' => __('booking.validation.price_numeric'),
            'unit_price.min' => __('booking.validation.price_min'),
            'total_amount.required' => __('booking.validation.total_required'),
            'total_amount.numeric' => __('booking.validation.total_numeric'),
            'total_amount.min' => __('booking.validation.total_min'),
            'extra_guests_count.max' => __('booking.validation.extra_guests_max'),
            'notes.max' => __('booking.validation.notes_max')
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'chef_id' => __('booking.fields.chef'),
            'chef_service_id' => __('booking.fields.service'),
            'address_id' => __('booking.fields.address'),
            'date' => __('booking.fields.date'),
            'start_time' => __('booking.fields.start_time'),
            'hours_count' => __('booking.fields.hours_count'),
            'number_of_guests' => __('booking.fields.number_of_guests'),
            'service_type' => __('booking.fields.service_type'),
            'unit_price' => __('booking.fields.unit_price'),
            'extra_guests_count' => __('booking.fields.extra_guests_count'),
            'extra_guests_amount' => __('booking.fields.extra_guests_amount'),
            'total_amount' => __('booking.fields.total_amount'),
            'commission_amount' => __('booking.fields.commission_amount'),
            'payment_status' => __('booking.fields.payment_status'),
            'booking_status' => __('booking.fields.booking_status'),
            'notes' => __('booking.fields.notes')
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation for business rules
            $this->validateBookingTime($validator);
            $this->validateServiceType($validator);
            $this->validatePricing($validator);
        });
    }

    /**
     * Validate booking time constraints
     */
    protected function validateBookingTime($validator)
    {
        $date = $this->input('date');
        $startTime = $this->input('start_time');
        $hoursCount = $this->input('hours_count');

        if ($date && $startTime && $hoursCount) {
            // Check if booking is within allowed hours (8 AM to 10 PM)
            $startHour = (int) substr($startTime, 0, 2);
            $endHour = $startHour + $hoursCount;

            if ($startHour < 8) {
                $validator->errors()->add('start_time', __('booking.validation.start_time_too_early'));
            }

            if ($endHour > 22) {
                $validator->errors()->add('hours_count', __('booking.validation.end_time_too_late'));
            }

            // Check if booking is not too far in the future (90 days)
            $maxAdvanceDays = config('booking.advance_booking_days', 90);
            $bookingDate = \Carbon\Carbon::parse($date);
            $maxDate = now()->addDays($maxAdvanceDays);

            if ($bookingDate->gt($maxDate)) {
                $validator->errors()->add('date', __('booking.validation.date_too_far', ['days' => $maxAdvanceDays]));
            }
        }
    }

    /**
     * Validate service type constraints
     */
    protected function validateServiceType($validator)
    {
        $serviceType = $this->input('service_type');
        $hoursCount = $this->input('hours_count');

        if ($serviceType === 'package' && $hoursCount && $hoursCount < 2) {
            $validator->errors()->add('hours_count', __('booking.validation.package_min_hours'));
        }
    }

    /**
     * Validate pricing consistency
     */
    protected function validatePricing($validator)
    {
        $unitPrice = $this->input('unit_price');
        $hoursCount = $this->input('hours_count');
        $extraGuestsAmount = $this->input('extra_guests_amount', 0);
        $totalAmount = $this->input('total_amount');
        $serviceType = $this->input('service_type');

        if ($unitPrice && $hoursCount && $totalAmount) {
            $expectedTotal = $serviceType === 'hourly' 
                ? ($unitPrice * $hoursCount) + $extraGuestsAmount
                : $unitPrice + $extraGuestsAmount;

            // Allow small rounding differences
            if (abs($expectedTotal - $totalAmount) > 0.01) {
                $validator->errors()->add('total_amount', __('booking.validation.total_amount_mismatch'));
            }
        }

        // Validate extra guests
        $extraGuestsCount = $this->input('extra_guests_count', 0);
        if ($extraGuestsCount > 0 && (!$extraGuestsAmount || $extraGuestsAmount <= 0)) {
            $validator->errors()->add('extra_guests_amount', __('booking.validation.extra_guests_amount_required'));
        }
    }
}