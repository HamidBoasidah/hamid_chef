<template>
  <div class="bg-red-50 border border-red-200 rounded-md p-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="ml-3 flex-1">
        <h3 class="text-sm font-medium text-red-800">
          {{ $t('booking.booking_conflicts_detected') }}
        </h3>
        <div class="mt-2 text-sm text-red-700">
          <p class="mb-3">{{ $t('booking.conflict_description') }}</p>
          
          <div class="space-y-3">
            <div
              v-for="booking in conflictingBookings"
              :key="booking.id || `${booking.date}-${booking.start_time}`"
              class="bg-white border border-red-300 rounded-lg p-3"
            >
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ formatDate(booking.date) }}</span>
                  </div>
                  
                  <div class="flex items-center space-x-2 mt-1">
                    <svg class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ formatTime(booking.start_time) }} - {{ formatTime(booking.end_time) }}</span>
                    <span v-if="booking.hours_count" class="text-xs text-gray-600">
                      ({{ $t('booking.hours_duration', { hours: booking.hours_count }) }})
                    </span>
                  </div>

                  <!-- Conflict Type -->
                  <div class="mt-2">
                    <span
                      v-if="booking.violation_type"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                      :class="getViolationTypeClass(booking.violation_type)"
                    >
                      {{ $t(`booking.violation_types.${booking.violation_type}`) }}
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                    >
                      {{ $t('booking.time_overlap') }}
                    </span>
                  </div>

                  <!-- Gap Information -->
                  <div v-if="booking.gap_hours !== undefined" class="mt-2 text-xs text-red-600">
                    <div class="flex items-center space-x-1">
                      <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                      </svg>
                      <span>
                        {{ $t('booking.gap_violation_detail', { 
                          current: booking.gap_hours, 
                          required: booking.required_gap || 2 
                        }) }}
                      </span>
                    </div>
                  </div>

                  <!-- Customer Information -->
                  <div v-if="booking.customer_name" class="mt-2 text-xs text-gray-600">
                    <div class="flex items-center space-x-1">
                      <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                      </svg>
                      <span>{{ booking.customer_name }}</span>
                    </div>
                  </div>
                </div>

                <!-- Booking ID -->
                <div v-if="booking.id" class="text-xs text-gray-500">
                  #{{ booking.id }}
                </div>
              </div>
            </div>
          </div>

          <!-- Resolution Suggestions -->
          <div class="mt-4 p-3 bg-red-100 rounded-md">
            <h4 class="text-sm font-medium text-red-800 mb-2">
              {{ $t('booking.resolution_suggestions') }}
            </h4>
            <ul class="text-xs text-red-700 space-y-1">
              <li class="flex items-start space-x-2">
                <span class="text-red-500 mt-0.5">•</span>
                <span>{{ $t('booking.suggestion_different_time') }}</span>
              </li>
              <li class="flex items-start space-x-2">
                <span class="text-red-500 mt-0.5">•</span>
                <span>{{ $t('booking.suggestion_different_date') }}</span>
              </li>
              <li class="flex items-start space-x-2">
                <span class="text-red-500 mt-0.5">•</span>
                <span>{{ $t('booking.suggestion_different_chef') }}</span>
              </li>
              <li v-if="hasGapViolations" class="flex items-start space-x-2">
                <span class="text-red-500 mt-0.5">•</span>
                <span>{{ $t('booking.suggestion_gap_requirement') }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// Props
const props = defineProps({
  conflictingBookings: {
    type: Array,
    required: true,
    default: () => []
  }
})

// Computed properties
const hasGapViolations = computed(() => {
  return props.conflictingBookings.some(booking => 
    booking.violation_type && booking.violation_type.includes('gap')
  )
})

// Methods
const formatDate = (dateString) => {
  if (!dateString) return ''
  
  return new Date(dateString).toLocaleDateString('ar-SA', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getViolationTypeClass = (violationType) => {
  const baseClasses = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium'
  
  switch (violationType) {
    case 'insufficient_gap_before':
    case 'insufficient_gap_after':
      return `${baseClasses} bg-yellow-100 text-yellow-800`
    case 'time_overlap':
    default:
      return `${baseClasses} bg-red-100 text-red-800`
  }
}

const pad = (n) => String(n).padStart(2, '0')

const isoToLocalTime = (iso) => {
  if (!iso || typeof iso !== 'string') return ''
  if (!iso.includes('T')) return iso
  const d = new Date(iso)
  if (isNaN(d)) return iso
  return `${pad(d.getHours())}:${pad(d.getMinutes())}`
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
</script>