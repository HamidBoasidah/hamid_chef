<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content - Booking Information -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Booking Information -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800 flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-200">
              <CalenderIcon class="h-8 w-8" />
            </div>
            <div>
              <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('booking.booking_details') }}</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.booking') }} #{{ booking.id }}</p>
            </div>
          </div>

          <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 gap-x-5 gap-y-6 md:grid-cols-2">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.date') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ formatDate(booking.date) }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.start_time') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ formatTime(booking.start_time) }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.hours_count') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.hours_count }} {{ t('booking.hours') }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.end_time') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ calculateEndTime(booking.start_time, booking.hours_count) }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.number_of_guests') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.number_of_guests }}</p>
              </div>

              <div v-if="booking.extra_guests_count > 0">
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.extra_guests_count') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.extra_guests_count }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.service_type') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ t(`booking.${booking.service_type}`) }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.booking_status') }}</label>
                <Badge :color="getStatusColor(booking.booking_status)" size="sm">
                  {{ t(`booking.${booking.booking_status}`) }}
                </Badge>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.payment_status') }}</label>
                <Badge :color="getPaymentStatusColor(booking.payment_status)" size="sm">
                  {{ t(`booking.${booking.payment_status}`) }}
                </Badge>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.created_at') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ formatDateTime(booking.created_at) }}</p>
              </div>

              <div v-if="booking.notes" class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('booking.notes') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90 whitespace-pre-wrap">{{ booking.notes }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Address Information -->
        <div v-if="booking.address" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('booking.address_information') }}</h2>
          </div>

          <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 gap-x-5 gap-y-6 md:grid-cols-2">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.address') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.address?.address ?? '—' }}</p>
              </div>

              <div v-if="booking.address?.label">
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('addresses.label') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.address.label }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pricing Details -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('booking.pricing_details') }}</h2>
          </div>

          <div class="p-4 sm:p-6">
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.unit_price') }}</span>
                <span class="text-sm text-gray-900 dark:text-white/90">{{ formatPrice(booking.unit_price) }}</span>
              </div>
              <div v-if="booking.service_type === 'hourly'" class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.hours_count') }} ({{ booking.hours_count }})</span>
                <span class="text-sm text-gray-900 dark:text-white/90">{{ formatPrice(booking.unit_price * booking.hours_count) }}</span>
              </div>
              <div v-if="booking.extra_guests_amount > 0" class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.extra_guests') }} ({{ booking.extra_guests_count }})</span>
                <span class="text-sm text-gray-900 dark:text-white/90">{{ formatPrice(booking.extra_guests_amount) }}</span>
              </div>
              <div v-if="booking.commission_amount > 0" class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('booking.commission') }}</span>
                <span class="text-sm text-gray-900 dark:text-white/90">{{ formatPrice(booking.commission_amount) }}</span>
              </div>
              <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-between">
                <span class="text-base font-medium text-gray-900 dark:text-white/90">{{ t('booking.total_amount') }}</span>
                <span class="text-base font-medium text-gray-900 dark:text-white/90">{{ formatPrice(booking.total_amount) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar - Customer, Chef Info and Actions -->
      <div class="space-y-6">
        <!-- Customer Information -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800 flex items-center gap-4">
            <div class="h-14 w-14 overflow-hidden rounded-full border border-gray-200 dark:border-gray-800">
              <img v-if="booking.customer?.image" :src="`/storage/${booking.customer.image}`" alt="" class="h-14 w-14 object-cover" />
              <div v-else class="h-14 w-14 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>

            <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('booking.customer_information') }}</h2>
          </div>

          <div class="p-4 sm:p-6">
            <div class="space-y-4">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.name') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.customer?.first_name }} {{ booking.customer?.last_name }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.phoneNumber') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.customer?.phone ?? '—' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Chef Information -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800 flex items-center gap-4">
            <div class="h-14 w-14 overflow-hidden rounded-full border border-gray-200 dark:border-gray-800">
              <img v-if="booking.chef?.logo" :src="`/storage/${booking.chef.logo}`" alt="" class="h-14 w-14 object-cover" />
              <div v-else class="h-14 w-14 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <ChefIcon class="h-8 w-8 text-gray-400" />
              </div>
            </div>

            <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('booking.chef_information') }}</h2>
          </div>

          <div class="p-4 sm:p-6">
            <div class="space-y-4">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.name') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.chef?.name ?? '—' }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.phoneNumber') }}</label>
                <p class="text-base text-gray-800 dark:text-white/90">{{ booking.chef?.phone ?? '—' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
          <Link :href="route('admin.bookings.edit', booking.id)" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
            {{ t('buttons.edit') }}
          </Link>

          <Link :href="route('admin.bookings.index')" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
            {{ t('buttons.backToList') }}
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { ChefIcon, CalenderIcon } from '@/icons'
import Badge from '@/Components/ui/Badge.vue'

const { t } = useI18n()

const props = defineProps({
  booking: {
    type: Object,
    required: true
  }
})

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ar-SA', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (dateTimeString) => {
  return new Date(dateTimeString).toLocaleString('ar-SA')
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('ar-SA', {
    style: 'currency',
    currency: 'SAR'
  }).format(price)
}

const pad = (n) => String(n).padStart(2, '0')

const parseStartTime = (startTime) => {
  if (!startTime) return { hours: 0, minutes: 0 }
  if (typeof startTime !== 'string') startTime = String(startTime)
  // ISO datetime
  if (startTime.includes('T')) {
    const d = new Date(startTime)
    if (!isNaN(d)) return { hours: d.getHours(), minutes: d.getMinutes() }
  }
  // H:i:s or H:i
  if (startTime.includes(':')) {
    const parts = startTime.split(':').map(Number)
    return { hours: parts[0] || 0, minutes: parts[1] || 0 }
  }
  return { hours: 0, minutes: 0 }
}

const calculateEndTime = (startTime, hoursCount) => {
  const { hours, minutes } = parseStartTime(startTime)
  const end = new Date()
  end.setHours(hours + (parseInt(hoursCount) || 0), minutes)
  return `${pad(end.getHours())}:${pad(end.getMinutes())}`
}

const formatTime = (val) => {
  if (!val) return ''
  if (typeof val !== 'string') return String(val)
  if (val.includes('T')) {
    const d = new Date(val)
    if (isNaN(d)) return val
    return `${pad(d.getHours())}:${pad(d.getMinutes())}`
  }
  if (val.includes(':')) {
    const parts = val.split(':')
    return `${pad(parts[0])}:${pad(parts[1] || '00')}`
  }
  return val
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    accepted: 'success',
    rejected: 'error',
    completed: 'info',
    cancelled_by_customer: 'light',
    cancelled_by_chef: 'light',
    cancelled_by_admin: 'light'
  }
  return colors[status] || 'light'
}

const getPaymentStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    paid: 'success',
    refunded: 'info',
    failed: 'error'
  }
  return colors[status] || 'light'
}
</script>