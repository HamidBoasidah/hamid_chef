<template>
  <div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('booking.booking_details') }}</h2>
      </div>
      <div class="p-4 sm:p-6 dark:border-gray-800">
        <!-- General Errors Display -->
        <div v-if="form.hasErrors && (form.errors.conflict || form.errors.error)" class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                {{ t('booking.booking_creation_error') }}
              </h3>
              <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                <p v-if="form.errors.conflict">{{ form.errors.conflict }}</p>
                <p v-else-if="form.errors.error">{{ form.errors.error }}</p>
              </div>
              <!-- Show conflicting bookings if available -->
              <div v-if="form.errors.conflicting_bookings && form.errors.conflicting_bookings.length > 0" class="mt-3">
                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">{{ t('booking.conflicting_bookings') }}:</h4>
                <ul class="mt-1 list-disc list-inside text-sm text-red-700 dark:text-red-300">
                  <li v-for="booking in form.errors.conflicting_bookings" :key="booking.id">
                    {{ t('booking.booking') }} #{{ booking.id }} - {{ booking.date }} {{ formatTime(booking.start_time) }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <form @submit.prevent>
          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <!-- Customer Selection -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.customer') }}</label>
              <select v-model="form.customer_id" @change="onCustomerChange" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option :value="null">{{ t('common.selectCustomer') }}</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ `${customer.first_name} ${customer.last_name} - ${customer.email}` }}</option>
              </select>
              <p v-if="form.errors.customer_id" class="mt-1 text-sm text-error-500">{{ form.errors.customer_id }}</p>
            </div>

            <!-- Chef Selection -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.chef') }}</label>
              <select v-model="form.chef_id" @change="onChefChange" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option :value="null">{{ t('booking.select_chef') }}</option>
                <option v-for="chef in chefs" :key="chef.id" :value="chef.id">{{ chef.name }}</option>
              </select>
              <p v-if="form.errors.chef_id" class="mt-1 text-sm text-error-500">{{ form.errors.chef_id }}</p>
            </div>

            <!-- Service Selection -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.service') }}</label>
              <select v-model="form.chef_service_id" @change="onServiceChange" :disabled="!form.chef_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 disabled:opacity-50">
                <option :value="null">{{ t('booking.select_service') }}</option>
                <option v-for="service in availableServices" :key="service.id" :value="service.id">
                  {{ service.name }} - {{ formatPrice(service.service_type === 'hourly' ? service.hourly_rate : service.package_price) }}
                </option>
              </select>
              <p v-if="form.errors.chef_service_id" class="mt-1 text-sm text-error-500">{{ form.errors.chef_service_id }}</p>
            </div>

            <!-- Address Selection (filtered by customer) -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.address') }}</label>
              <select v-model="form.address_id" :disabled="!form.customer_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 disabled:opacity-50">
                <option :value="null">{{ t('booking.select_address') }}</option>
                <option v-for="address in customerAddresses" :key="address.id" :value="address.id">
                  {{ address.label ? `${address.label} - ${address.address}` : address.address }}
                </option>
              </select>
              <p v-if="form.errors.address_id" class="mt-1 text-sm text-error-500">{{ form.errors.address_id }}</p>
            </div>

            <!-- Date -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.date') }}</label>
              <input v-model="form.date" type="date" :min="today" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              <p v-if="form.errors.date" class="mt-1 text-sm text-error-500">{{ form.errors.date }}</p>
            </div>

            <!-- Start Time -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.start_time') }}</label>
              <div class="relative">
                <flat-pickr
                  v-model="form.start_time"
                  :config="flatpickrTimeConfig"
                  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                  :placeholder="t('booking.select_time')"
                />
                <span class="absolute text-gray-500 -translate-y-1/2 right-3 top-1/2 dark:text-gray-400">
                  <svg
                    class="fill-current"
                    width="20"
                    height="20"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M3.04175 9.99984C3.04175 6.15686 6.1571 3.0415 10.0001 3.0415C13.8431 3.0415 16.9584 6.15686 16.9584 9.99984C16.9584 13.8428 13.8431 16.9582 10.0001 16.9582C6.1571 16.9582 3.04175 13.8428 3.04175 9.99984ZM10.0001 1.5415C5.32867 1.5415 1.54175 5.32843 1.54175 9.99984C1.54175 14.6712 5.32867 18.4582 10.0001 18.4582C14.6715 18.4582 18.4584 14.6712 18.4584 9.99984C18.4584 5.32843 14.6715 1.5415 10.0001 1.5415ZM9.99998 10.7498C9.58577 10.7498 9.24998 10.4141 9.24998 9.99984V5.4165C9.24998 5.00229 9.58577 4.6665 9.99998 4.6665C10.4142 4.6665 10.75 5.00229 10.75 5.4165V9.24984H13.3334C13.7476 9.24984 14.0834 9.58562 14.0834 9.99984C14.0834 10.4141 13.7476 10.7498 13.3334 10.7498H10.0001H9.99998Z"
                      fill=""
                    />
                  </svg>
                </span>
              </div>
              <p v-if="form.errors.start_time" class="mt-1 text-sm text-error-500">{{ form.errors.start_time }}</p>
            </div>

            <!-- Hours Count (only show for hourly services) -->
            <div v-if="form.service_type === 'hourly'">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.hours_count') }}</label>
              <input v-model="form.hours_count" type="number" :min="selectedService?.min_hours || 1" max="12" @input="calculateTotal" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
              <p v-if="selectedService?.min_hours" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ t('booking.minimum_hours', { hours: selectedService.min_hours }) }}</p>
              <p v-if="form.errors.hours_count" class="mt-1 text-sm text-error-500">{{ form.errors.hours_count }}</p>
            </div>

            <!-- Number of Guests (only show for hourly services, read-only) -->
            <div v-if="form.service_type === 'hourly'">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.number_of_guests') }}</label>
              <input v-model="numberOfGuestsDisplay" type="text" readonly class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" />
              <p v-if="selectedService?.max_guests_included" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ t('booking.max_included_guests', { guests: selectedService.max_guests_included }) }}</p>
              <p v-if="form.errors.number_of_guests" class="mt-1 text-sm text-error-500">{{ form.errors.number_of_guests }}</p>
            </div>

            <!-- Service Type (read-only, auto-filled) -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.service_type') }}</label>
              <input v-model="serviceTypeDisplay" type="text" readonly class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" />
              <p v-if="form.errors.service_type" class="mt-1 text-sm text-error-500">{{ form.errors.service_type }}</p>
            </div>

            <!-- Unit Price (read-only, auto-filled) -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.unit_price') }}</label>
              <input v-model="unitPriceDisplay" type="text" readonly class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" />
              <p v-if="form.errors.unit_price" class="mt-1 text-sm text-error-500">{{ form.errors.unit_price }}</p>
            </div>

            <!-- Extra Guests Count (only show if service allows extra guests) -->
            <div v-if="showExtraGuestsFields">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.extra_guests_count') }}</label>
              <input v-model="form.extra_guests_count" type="number" min="0" max="20" @input="calculateExtraGuestsAmount" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
              <p v-if="selectedService?.extra_guest_price" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ t('booking.extra_guest_price_info', { price: formatPrice(selectedService.extra_guest_price) }) }}</p>
              <p v-if="form.errors.extra_guests_count" class="mt-1 text-sm text-error-500">{{ form.errors.extra_guests_count }}</p>
            </div>

            <!-- Extra Guests Amount (auto-calculated) -->
            <div v-if="showExtraGuestsFields">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.extra_guests_amount') }}</label>
              <input v-model="extraGuestsAmountDisplay" type="text" readonly class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" />
              <p v-if="form.errors.extra_guests_amount" class="mt-1 text-sm text-error-500">{{ form.errors.extra_guests_amount }}</p>
            </div>

            <!-- Total Amount (Read-only) -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.total_amount') }}</label>
              <input v-model="totalAmountDisplay" type="text" readonly class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" />
              <p v-if="form.errors.total_amount" class="mt-1 text-sm text-error-500">{{ form.errors.total_amount }}</p>
            </div>

            <!-- Payment Status -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.payment_status') }}</label>
              <select v-model="form.payment_status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="pending">{{ t('booking.pending') }}</option>
                <option value="paid">{{ t('booking.paid') }}</option>
                <option value="refunded">{{ t('booking.refunded') }}</option>
                <option value="failed">{{ t('booking.failed') }}</option>
              </select>
              <p v-if="form.errors.payment_status" class="mt-1 text-sm text-error-500">{{ form.errors.payment_status }}</p>
            </div>

            <!-- Booking Status -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.booking_status') }}</label>
              <select v-model="form.booking_status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="pending">{{ t('booking.pending') }}</option>
                <option value="accepted">{{ t('booking.accepted') }}</option>
                <option value="rejected">{{ t('booking.rejected') }}</option>
                <option value="completed">{{ t('booking.completed') }}</option>
              </select>
              <p v-if="form.errors.booking_status" class="mt-1 text-sm text-error-500">{{ form.errors.booking_status }}</p>
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('booking.notes') }}</label>
              <textarea v-model="form.notes" rows="4" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" :placeholder="t('booking.notes_placeholder')"></textarea>
              <p v-if="form.errors.notes" class="mt-1 text-sm text-error-500">{{ form.errors.notes }}</p>
            </div>

            <!-- Active Status -->
            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.status') }}</label>
              <div class="mb-6 flex flex-wrap items-center gap-6 sm:gap-8">
                <div>
                  <label
                    for="toggle-active"
                    class="flex cursor-pointer select-none items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-400"
                  >
                    <div class="relative">
                      <input type="checkbox" id="toggle-active" class="sr-only" v-model="form.is_active" />
                      <div
                        class="block h-6 w-11 rounded-full"
                        :class="form.is_active ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'"
                      ></div>
                      <div
                        :class="form.is_active ? 'translate-x-full' : 'translate-x-0'"
                        class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-theme-sm duration-300 ease-linear"
                      ></div>
                    </div>
                    <span
                      class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                      :class="{
                        'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500': form.is_active,
                        'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500': !form.is_active,
                      }"
                    >
                      {{ form.is_active ? t('common.active') : t('common.inactive') }}
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Pricing Summary -->
    <div v-if="showPricingSummary" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('booking.pricing_summary') }}</h2>
      </div>
      <div class="p-4 sm:p-6">
        <div class="space-y-4">
          <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.unit_price') }}</span>
            <span class="text-sm text-gray-900 dark:text-white/90">{{ formatPrice(form.unit_price) }}</span>
          </div>
          <div v-if="form.service_type === 'hourly'" class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.hours_count') }} ({{ form.hours_count }})</span>
            <span class="text-sm text-gray-900 dark:text-white/90">{{ formatPrice(form.unit_price * form.hours_count) }}</span>
          </div>
          <div v-if="form.extra_guests_amount > 0" class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.extra_guests') }} ({{ form.extra_guests_count }})</span>
            <span class="text-sm text-gray-900 dark:text-white/90">{{ formatPrice(form.extra_guests_amount) }}</span>
          </div>
          <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-between">
            <span class="text-base font-medium text-gray-900 dark:text-white/90">{{ t('booking.total_amount') }}</span>
            <span class="text-base font-medium text-gray-900 dark:text-white/90">{{ formatPrice(form.total_amount) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
      <Link :href="route('admin.bookings.show', booking.id)" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">{{ t('buttons.cancel') }}</Link>
      <button @click="update" :disabled="form.processing" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition disabled:opacity-50">
        <span v-if="form.processing">{{ t('buttons.updating') }}...</span>
        <span v-else>{{ t('buttons.update') }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { computed, watch, onMounted } from 'vue'
import { useNotifications } from '@/composables/useNotifications'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

const { t } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({ 
  booking: Object,
  customers: Array, 
  chefs: Array, 
  addresses: Array,
  chefServices: Array
})

// Flatpickr configuration for time selection
const flatpickrTimeConfig = {
  enableTime: true,
  noCalendar: true,
  dateFormat: "H:i",
  time_24hr: true,
  minuteIncrement: 15,
  defaultHour: 8,
  minTime: "08:00",
  maxTime: "22:00"
}

const pad = (n) => String(n).padStart(2, '0')

const isoToLocalTime = (iso) => {
  if (!iso || typeof iso !== 'string') return ''
  if (!iso.includes('T')) return iso
  const d = new Date(iso)
  if (isNaN(d)) return iso
  return `${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const isoToLocalDate = (iso) => {
  if (!iso || typeof iso !== 'string') return ''
  if (!iso.includes('T')) return iso
  const d = new Date(iso)
  if (isNaN(d)) return iso.split('T')[0] || iso
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
}

const normalizeDateTimeInputs = () => {
  if (form.start_time && typeof form.start_time === 'string' && form.start_time.includes('T')) {
    form.start_time = isoToLocalTime(form.start_time)
  }
  if (form.date && typeof form.date === 'string' && form.date.includes('T')) {
    form.date = isoToLocalDate(form.date)
  }
}

const formatTime = (val) => {
  if (!val) return ''
  if (typeof val !== 'string') return String(val)
  if (val.includes('T')) return isoToLocalTime(val)
  if (val.includes(':')) {
    const parts = val.split(':')
    return `${pad(parts[0])}:${pad(parts[1] || '00')}`
  }
  return val
}

const form = useForm({
  customer_id: props.booking.customer_id,
  chef_id: props.booking.chef_id,
  chef_service_id: props.booking.chef_service_id,
  address_id: props.booking.address_id,
  date: props.booking.date,
  start_time: props.booking.start_time,
  hours_count: props.booking.hours_count,
  number_of_guests: props.booking.number_of_guests,
  service_type: props.booking.service_type,
  unit_price: props.booking.unit_price,
  extra_guests_count: props.booking.extra_guests_count || 0,
  extra_guests_amount: props.booking.extra_guests_amount || 0,
  total_amount: props.booking.total_amount,
  payment_status: props.booking.payment_status,
  booking_status: props.booking.booking_status,
  notes: props.booking.notes || '',
  is_active: props.booking.is_active
})

// Computed properties
const today = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const availableServices = computed(() => {
  if (!form.chef_id || !props.chefServices) return []
  return props.chefServices.filter(service => service.chef_id === form.chef_id)
})

const selectedService = computed(() => {
  if (!form.chef_service_id || !props.chefServices) return null
  return props.chefServices.find(service => service.id === form.chef_service_id)
})

const customerAddresses = computed(() => {
  if (!form.customer_id || !props.addresses) return []
  return props.addresses.filter(address => address.user_id === form.customer_id)
})

const showExtraGuestsFields = computed(() => {
  return selectedService.value && 
         selectedService.value.service_type === 'hourly' && 
         selectedService.value.allow_extra_guests
})

const showPricingSummary = computed(() => {
  return form.unit_price > 0 && form.hours_count > 0
})

// Display computed properties
const serviceTypeDisplay = computed(() => {
  return form.service_type ? t(`booking.${form.service_type}`) : ''
})

const unitPriceDisplay = computed(() => {
  return formatPrice(form.unit_price)
})

const numberOfGuestsDisplay = computed(() => {
  return form.number_of_guests ? `${form.number_of_guests} ${t('booking.guests')}` : ''
})

const extraGuestsAmountDisplay = computed(() => {
  return formatPrice(form.extra_guests_amount)
})

const totalAmountDisplay = computed(() => {
  return formatPrice(form.total_amount)
})

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('ar-SA', {
    style: 'currency',
    currency: 'SAR'
  }).format(price || 0)
}

const onCustomerChange = () => {
  // Reset address when customer changes
  form.address_id = null
}

const onChefChange = () => {
  // Reset service and related fields when chef changes
  form.chef_service_id = null
  form.service_type = 'hourly'
  form.unit_price = 0
  form.hours_count = 1
  form.number_of_guests = 1
  form.extra_guests_count = 0
  form.extra_guests_amount = 0
  calculateTotal()
}

const onServiceChange = () => {
  const service = selectedService.value
  if (service) {
    // Auto-fill service information
    form.service_type = service.service_type
    form.unit_price = service.service_type === 'hourly' ? service.hourly_rate : service.package_price
    
    if (service.service_type === 'hourly') {
      // For hourly services
      // Set minimum hours if specified
      if (service.min_hours && form.hours_count < service.min_hours) {
        form.hours_count = service.min_hours
      }
      
      // Set default number of guests to max included
      if (service.max_guests_included) {
        form.number_of_guests = Math.min(form.number_of_guests, service.max_guests_included)
      }
    } else {
      // For package services - set fixed values
      form.hours_count = 1 // Not used in calculation for packages
      form.number_of_guests = service.max_guests_included || 1 // Fixed for packages
    }
    
    // Reset extra guests
    form.extra_guests_count = 0
    form.extra_guests_amount = 0
  }
  calculateTotal()
}

const calculateExtraGuestsAmount = () => {
  const service = selectedService.value
  if (service && service.allow_extra_guests && service.extra_guest_price) {
    const extraGuestsCount = parseInt(form.extra_guests_count) || 0
    form.extra_guests_amount = extraGuestsCount * service.extra_guest_price
  } else {
    form.extra_guests_amount = 0
  }
  calculateTotal()
}

const calculateTotal = () => {
  const unitPrice = parseFloat(form.unit_price) || 0
  const hoursCount = parseInt(form.hours_count) || 1
  const extraGuestsAmount = parseFloat(form.extra_guests_amount) || 0

  let baseAmount = 0
  if (form.service_type === 'hourly') {
    baseAmount = unitPrice * hoursCount
  } else {
    baseAmount = unitPrice
  }

  form.total_amount = baseAmount + extraGuestsAmount
}

const update = () => {
  // Clear previous errors
  form.clearErrors()
  
  form.put(route('admin.bookings.update', props.booking.id), {
    onSuccess: () => {
      success(t('booking.booking_updated_successfully'))
    },
    onError: (errors) => {
      // Errors will be displayed under each field automatically
      // No notification needed
    },
  })
}

// Watch for changes that should trigger total calculation
watch([
  () => form.unit_price,
  () => form.hours_count,
  () => form.extra_guests_amount,
  () => form.service_type
], calculateTotal)

// Watch for extra guests count changes
watch(() => form.extra_guests_count, calculateExtraGuestsAmount)

// Initialize calculations on mount
onMounted(() => {
  calculateTotal()
  normalizeDateTimeInputs()
})
</script>