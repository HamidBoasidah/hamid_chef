<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-medium text-gray-900">
        {{ $t('booking.select_time_slot') }}
      </h3>
      <div class="flex items-center space-x-2 text-sm text-gray-600">
        <div class="flex items-center space-x-1">
          <div class="w-3 h-3 bg-green-100 border border-green-300 rounded"></div>
          <span>{{ $t('booking.available') }}</span>
        </div>
        <div class="flex items-center space-x-1">
          <div class="w-3 h-3 bg-red-100 border border-red-300 rounded"></div>
          <span>{{ $t('booking.unavailable') }}</span>
        </div>
        <div class="flex items-center space-x-1">
          <div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded"></div>
          <span>{{ $t('booking.selected') }}</span>
        </div>
      </div>
    </div>

    <!-- Date Navigation -->
    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
      <button
        @click="previousDate"
        :disabled="!canGoPrevious"
        class="p-2 rounded-md hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
      
      <div class="text-center">
        <div class="text-lg font-semibold text-gray-900">
          {{ formatSelectedDate() }}
        </div>
        <div class="text-sm text-gray-600">
          {{ formatSelectedDateDetails() }}
        </div>
      </div>
      
      <button
        @click="nextDate"
        :disabled="!canGoNext"
        class="p-2 rounded-md hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center py-8">
      <div class="flex items-center space-x-2">
        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm text-gray-600">{{ $t('booking.loading_time_slots') }}</span>
      </div>
    </div>

    <!-- Time Slots Grid -->
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
      <button
        v-for="slot in timeSlots"
        :key="slot.time"
        @click="selectTimeSlot(slot)"
        :disabled="!slot.available"
        class="relative p-3 text-sm font-medium rounded-lg border-2 transition-all duration-200"
        :class="getSlotClasses(slot)"
      >
        <div class="flex flex-col items-center space-y-1">
          <div class="font-semibold">{{ slot.time }}</div>
          <div class="text-xs opacity-75">{{ slot.endTime }}</div>
          <div v-if="slot.duration" class="text-xs opacity-60">
            {{ $t('booking.hours_duration', { hours: slot.duration }) }}
          </div>
        </div>

        <!-- Availability Indicator -->
        <div
          class="absolute top-1 right-1 w-2 h-2 rounded-full"
          :class="{
            'bg-green-400': slot.available && !slot.selected,
            'bg-red-400': !slot.available,
            'bg-blue-400': slot.selected
          }"
        ></div>

        <!-- Conflict Indicator -->
        <div v-if="slot.hasConflict" class="absolute bottom-1 left-1">
          <svg class="h-3 w-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </div>
      </button>
    </div>

    <!-- Selected Time Summary -->
    <div v-if="selectedSlot" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-sm font-medium text-blue-800">{{ $t('booking.selected_time_slot') }}</h4>
          <div class="mt-1 text-sm text-blue-700">
            <span class="font-semibold">{{ formatSelectedDate() }}</span>
            <span class="mx-2">•</span>
            <span>{{ selectedSlot.time }} - {{ selectedSlot.endTime }}</span>
            <span class="mx-2">•</span>
            <span>{{ $t('booking.hours_duration', { hours: selectedSlot.duration }) }}</span>
          </div>
        </div>
        <button
          @click="clearSelection"
          class="text-blue-600 hover:text-blue-800 text-sm font-medium"
        >
          {{ $t('common.clear') }}
        </button>
      </div>
    </div>

    <!-- No Available Slots Message -->
    <div v-if="!isLoading && timeSlots.length === 0" class="text-center py-8">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('booking.no_available_slots') }}</h3>
      <p class="mt-1 text-sm text-gray-500">{{ $t('booking.try_different_date') }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// Props
const props = defineProps({
  chefId: {
    type: [Number, String],
    required: true
  },
  duration: {
    type: Number,
    default: 1
  },
  selectedDate: {
    type: String,
    default: () => new Date().toISOString().split('T')[0]
  },
  selectedTime: {
    type: String,
    default: ''
  },
  excludeBookingId: {
    type: Number,
    default: null
  }
})

// Emits
const emit = defineEmits(['time-selected', 'date-changed'])

// Reactive data
const currentDate = ref(props.selectedDate)
const timeSlots = ref([])
const selectedSlot = ref(null)
const isLoading = ref(false)
const existingBookings = ref([])

// Computed properties
const canGoPrevious = computed(() => {
  const today = new Date().toISOString().split('T')[0]
  return currentDate.value > today
})

const canGoNext = computed(() => {
  const maxDate = new Date()
  maxDate.setDate(maxDate.getDate() + 90)
  return currentDate.value < maxDate.toISOString().split('T')[0]
})

// Methods
const previousDate = () => {
  if (!canGoPrevious.value) return
  
  const date = new Date(currentDate.value)
  date.setDate(date.getDate() - 1)
  currentDate.value = date.toISOString().split('T')[0]
}

const nextDate = () => {
  if (!canGoNext.value) return
  
  const date = new Date(currentDate.value)
  date.setDate(date.getDate() + 1)
  currentDate.value = date.toISOString().split('T')[0]
}

const formatSelectedDate = () => {
  return new Date(currentDate.value).toLocaleDateString('ar-SA', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatSelectedDateDetails = () => {
  const date = new Date(currentDate.value)
  const today = new Date()
  const tomorrow = new Date(today)
  tomorrow.setDate(tomorrow.getDate() + 1)
  
  if (date.toDateString() === today.toDateString()) {
    return t('booking.today')
  } else if (date.toDateString() === tomorrow.toDateString()) {
    return t('booking.tomorrow')
  }
  
  return ''
}

const generateTimeSlots = async () => {
  isLoading.value = true
  timeSlots.value = []
  
  try {
    // Fetch existing bookings for the date
    const response = await axios.get(`/api/chefs/${props.chefId}/bookings`, {
      params: { date: currentDate.value }
    })
    existingBookings.value = response.data.data
    
    // Generate time slots from 8 AM to 10 PM
    const slots = []
    for (let hour = 8; hour <= 22 - props.duration; hour++) {
      const startTime = `${hour.toString().padStart(2, '0')}:00`
      const endHour = hour + props.duration
      const endTime = `${endHour.toString().padStart(2, '0')}:00`
      
      const slot = {
        time: startTime,
        endTime: endTime,
        duration: props.duration,
        available: true,
        hasConflict: false,
        selected: startTime === props.selectedTime
      }
      
      // Check for conflicts with existing bookings
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

      const hasConflict = existingBookings.value.some(booking => {
        const bookingStart = parseHour(booking.start_time)
        const bookingEnd = parseHour(booking.end_time)

        // Check for overlap
        return (hour < bookingEnd && endHour > bookingStart)
      })
      
      if (hasConflict) {
        slot.available = false
        slot.hasConflict = true
      }
      
      // Check for gap violations (2 hours before/after)
      const hasGapViolation = existingBookings.value.some(booking => {
        const bookingStart = parseHour(booking.start_time)
        const bookingEnd = parseHour(booking.end_time)

        // Check if new slot is too close to existing booking
        const gapBefore = Math.abs(hour - bookingEnd)
        const gapAfter = Math.abs(bookingStart - endHour)

        return (gapBefore > 0 && gapBefore < 2) || (gapAfter > 0 && gapAfter < 2)
      })
      
      if (hasGapViolation) {
        slot.available = false
        slot.hasConflict = true
      }
      
      slots.push(slot)
    }
    
    timeSlots.value = slots
    
    // Update selected slot if it exists
    if (props.selectedTime) {
      selectedSlot.value = slots.find(slot => slot.time === props.selectedTime)
    }
    
  } catch (error) {
    console.error('Error generating time slots:', error)
  } finally {
    isLoading.value = false
  }
}

const selectTimeSlot = (slot) => {
  if (!slot.available) return
  
  // Clear previous selection
  timeSlots.value.forEach(s => s.selected = false)
  
  // Set new selection
  slot.selected = true
  selectedSlot.value = slot
  
  emit('time-selected', {
    date: currentDate.value,
    time: slot.time,
    endTime: slot.endTime,
    duration: slot.duration
  })
}

const clearSelection = () => {
  if (selectedSlot.value) {
    selectedSlot.value.selected = false
    selectedSlot.value = null
  }
  
  emit('time-selected', null)
}

const getSlotClasses = (slot) => {
  const baseClasses = 'relative p-3 text-sm font-medium rounded-lg border-2 transition-all duration-200'
  
  if (slot.selected) {
    return `${baseClasses} bg-blue-100 border-blue-300 text-blue-800 shadow-md`
  } else if (!slot.available) {
    return `${baseClasses} bg-red-50 border-red-200 text-red-600 cursor-not-allowed opacity-60`
  } else {
    return `${baseClasses} bg-green-50 border-green-200 text-green-800 hover:bg-green-100 hover:border-green-300 hover:shadow-md cursor-pointer`
  }
}

// Watchers
watch(currentDate, (newDate) => {
  emit('date-changed', newDate)
  generateTimeSlots()
})

watch(() => props.chefId, () => {
  if (props.chefId) {
    generateTimeSlots()
  }
})

watch(() => props.duration, () => {
  generateTimeSlots()
})

// Initialize
onMounted(() => {
  if (props.chefId) {
    generateTimeSlots()
  }
})

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