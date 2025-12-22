<template>
  <div class="bg-white border rounded-lg p-4">
    <div class="flex items-center space-x-3">
      <div v-if="isChecking" class="flex items-center">
        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="ml-2 text-sm text-gray-600">{{ $t('booking.checking_availability') }}</span>
      </div>

      <div v-else-if="availabilityResult" class="flex items-center">
        <div v-if="availabilityResult.available" class="flex items-center text-green-600">
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="ml-2 text-sm font-medium">{{ $t('booking.time_slot_available') }}</span>
        </div>

        <div v-else class="flex items-center text-red-600">
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <span class="ml-2 text-sm font-medium">{{ $t('booking.time_slot_unavailable') }}</span>
        </div>
      </div>
    </div>

    <!-- Error Messages -->
    <div v-if="availabilityResult && !availabilityResult.available && availabilityResult.errors" class="mt-3">
      <div class="bg-red-50 border border-red-200 rounded-md p-3">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
              {{ $t('booking.availability_issues') }}
            </h3>
            <div class="mt-2 text-sm text-red-700">
              <ul class="list-disc pl-5 space-y-1">
                <li v-for="error in availabilityResult.errors" :key="error">{{ error }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Conflicting Bookings -->
    <div v-if="availabilityResult && availabilityResult.conflicting_bookings && availabilityResult.conflicting_bookings.length > 0" class="mt-3">
      <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
              {{ $t('booking.conflicting_bookings') }}
            </h3>
            <div class="mt-2">
              <div class="space-y-2">
                <div
                  v-for="booking in availabilityResult.conflicting_bookings"
                  :key="booking.id"
                  class="bg-white border border-yellow-300 rounded p-2 text-sm"
                >
                  <div class="flex justify-between items-center">
                    <div>
                      <span class="font-medium">{{ formatDate(booking.date) }}</span>
                      <span class="text-gray-600 ml-2">
                        {{ formatTime(booking.start_time) }} - {{ formatTime(booking.end_time) }}
                      </span>
                    </div>
                    <div v-if="booking.gap_hours !== undefined" class="text-xs text-yellow-700">
                      {{ $t('booking.gap_violation', { hours: booking.gap_hours, required: booking.required_gap }) }}
                    </div>
                  </div>
                  <div v-if="booking.violation_type" class="text-xs text-yellow-600 mt-1">
                    {{ $t(`booking.violation_types.${booking.violation_type}`) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Alternative Time Suggestions -->
    <div v-if="availabilityResult && !availabilityResult.available && suggestedTimes.length > 0" class="mt-3">
      <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
        <h4 class="text-sm font-medium text-blue-800 mb-2">
          {{ $t('booking.suggested_times') }}
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
          <button
            v-for="suggestion in suggestedTimes"
            :key="suggestion.time"
            @click="$emit('time-selected', suggestion)"
            class="text-xs bg-white border border-blue-300 rounded px-2 py-1 hover:bg-blue-100 transition-colors"
          >
            {{ suggestion.time }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useDebounce } from '@/composables/useDebounce.js'

const { t } = useI18n()

// Props
const props = defineProps({
  chefId: {
    type: [Number, String],
    required: true
  },
  date: {
    type: String,
    required: true
  },
  startTime: {
    type: String,
    required: true
  },
  hoursCount: {
    type: Number,
    required: true
  },
  excludeBookingId: {
    type: Number,
    default: null
  }
})

// Emits
const emit = defineEmits(['availability-checked', 'time-selected'])

// Reactive data
const isChecking = ref(false)
const availabilityResult = ref(null)
const suggestedTimes = ref([])

// Computed properties
const shouldCheck = computed(() => {
  return props.chefId && props.date && props.startTime && props.hoursCount
})

// Use debounce composable
const { debouncedFn: checkAvailability } = useDebounce(async () => {
  if (!shouldCheck.value) return

  isChecking.value = true
  availabilityResult.value = null
  suggestedTimes.value = []

  try {
    const params = {
      date: props.date,
      start_time: props.startTime,
      hours_count: props.hoursCount
    }

    if (props.excludeBookingId) {
      params.exclude_booking_id = props.excludeBookingId
    }

    const response = await axios.get(`/api/chefs/${props.chefId}/availability`, { params })
    availabilityResult.value = response.data.data

    // Generate suggested times if not available
    if (!availabilityResult.value.available) {
      await generateSuggestedTimes()
    }

    emit('availability-checked', availabilityResult.value)
  } catch (error) {
    console.error('Error checking availability:', error)
    availabilityResult.value = {
      available: false,
      errors: [t('booking.availability_check_error')]
    }
    emit('availability-checked', availabilityResult.value)
  } finally {
    isChecking.value = false
  }
}, 500) // 500ms delay for availability checking

const generateSuggestedTimes = async () => {
  try {
    const response = await axios.get(`/api/chefs/${props.chefId}/bookings`, {
      params: { date: props.date }
    })
    
    const existingBookings = response.data.data
    const suggestions = []
    
    // Generate time slots from 8 AM to 10 PM
    for (let hour = 8; hour <= 22 - props.hoursCount; hour++) {
      const timeSlot = `${hour.toString().padStart(2, '0')}:00`
      
      // Skip if this is the current time being checked
      if (timeSlot === props.startTime) continue
      
      // Check if this time slot conflicts with existing bookings
      const endHour = hour + props.hoursCount
      const parseHour = (timeStr) => {
        if (!timeStr) return 0
        if (typeof timeStr !== 'string') timeStr = String(timeStr)
        if (timeStr.includes('T')) {
          const d = new Date(timeStr)
          if (!isNaN(d)) return d.getHours()
        }
        if (timeStr.includes(':')) return parseInt(timeStr.split(':')[0])
        return parseInt(timeStr) || 0
      }

      const hasConflict = existingBookings.some(booking => {
        const bookingStart = parseHour(booking.start_time)
        const bookingEnd = parseHour(booking.end_time)

        return (hour < bookingEnd && endHour > bookingStart)
      })
      
      if (!hasConflict && suggestions.length < 6) {
        suggestions.push({
          time: timeSlot,
          endTime: `${endHour.toString().padStart(2, '0')}:00`
        })
      }
    }
    
    suggestedTimes.value = suggestions
  } catch (error) {
    console.error('Error generating suggested times:', error)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ar-SA', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Watchers
watch(
  () => [props.chefId, props.date, props.startTime, props.hoursCount],
  () => {
    if (shouldCheck.value) {
      checkAvailability()
    }
  },
  { immediate: true }
)

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