<template>
  <form @submit.prevent="submitForm" class="space-y-6">
    <!-- Chef Selection -->
    <div>
      <label for="chef_id" class="block text-sm font-medium text-gray-700">
        {{ $t('booking.fields.chef') }} <span class="text-red-500">*</span>
      </label>
      <select
        id="chef_id"
        v-model="form.chef_id"
        @change="onChefChange"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-red-300': errors.chef_id }"
        required
      >
        <option value="">{{ $t('booking.select_chef') }}</option>
        <option v-for="chef in chefs" :key="chef.id" :value="chef.id">
          {{ chef.name }}
        </option>
      </select>
      <p v-if="errors.chef_id" class="mt-1 text-sm text-red-600">{{ errors.chef_id }}</p>
    </div>

    <!-- Service Selection -->
    <div>
      <label for="chef_service_id" class="block text-sm font-medium text-gray-700">
        {{ $t('booking.fields.service') }} <span class="text-red-500">*</span>
      </label>
      <select
        id="chef_service_id"
        v-model="form.chef_service_id"
        @change="onServiceChange"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-red-300': errors.chef_service_id }"
        :disabled="!availableServices.length"
        required
      >
        <option value="">{{ $t('booking.select_service') }}</option>
        <option v-for="service in availableServices" :key="service.id" :value="service.id">
          {{ service.name }} - {{ formatPrice(service.hourly_rate || service.package_price) }}
        </option>
      </select>
      <p v-if="errors.chef_service_id" class="mt-1 text-sm text-red-600">{{ errors.chef_service_id }}</p>
    </div>

    <!-- Date and Time -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="date" class="block text-sm font-medium text-gray-700">
          {{ $t('booking.fields.date') }} <span class="text-red-500">*</span>
        </label>
        <input
          id="date"
          v-model="form.date"
          @change="onDateTimeChange"
          type="date"
          :min="minDate"
          :max="maxDate"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-300': errors.date }"
          required
        />
        <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
      </div>

      <div>
        <label for="start_time" class="block text-sm font-medium text-gray-700">
          {{ $t('booking.fields.start_time') }} <span class="text-red-500">*</span>
        </label>
        <input
          id="start_time"
          v-model="form.start_time"
          @change="onDateTimeChange"
          type="time"
          min="08:00"
          max="22:00"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-300': errors.start_time }"
          required
        />
        <p v-if="errors.start_time" class="mt-1 text-sm text-red-600">{{ errors.start_time }}</p>
      </div>
    </div>

    <!-- Hours Count -->
    <div>
      <label for="hours_count" class="block text-sm font-medium text-gray-700">
        {{ $t('booking.fields.hours_count') }} <span class="text-red-500">*</span>
      </label>
      <input
        id="hours_count"
        v-model.number="form.hours_count"
        @input="onHoursChange"
        type="number"
        min="1"
        max="12"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-red-300': errors.hours_count }"
        required
      />
      <p v-if="errors.hours_count" class="mt-1 text-sm text-red-600">{{ errors.hours_count }}</p>
      <p v-if="form.start_time && form.hours_count" class="mt-1 text-sm text-gray-500">
        {{ $t('booking.end_time') }}: {{ calculateEndTime() }}
      </p>
    </div>

    <!-- Number of Guests -->
    <div>
      <label for="number_of_guests" class="block text-sm font-medium text-gray-700">
        {{ $t('booking.fields.number_of_guests') }} <span class="text-red-500">*</span>
      </label>
      <input
        id="number_of_guests"
        v-model.number="form.number_of_guests"
        @input="calculatePricing"
        type="number"
        min="1"
        max="50"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-red-300': errors.number_of_guests }"
        required
      />
      <p v-if="errors.number_of_guests" class="mt-1 text-sm text-red-600">{{ errors.number_of_guests }}</p>
    </div>

    <!-- Extra Guests -->
    <div v-if="selectedService && selectedService.allow_extra_guests">
      <label for="extra_guests_count" class="block text-sm font-medium text-gray-700">
        {{ $t('booking.fields.extra_guests_count') }}
      </label>
      <input
        id="extra_guests_count"
        v-model.number="form.extra_guests_count"
        @input="calculatePricing"
        type="number"
        min="0"
        max="20"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-red-300': errors.extra_guests_count }"
      />
      <p v-if="errors.extra_guests_count" class="mt-1 text-sm text-red-600">{{ errors.extra_guests_count }}</p>
      <p v-if="form.extra_guests_count > 0" class="mt-1 text-sm text-gray-500">
        {{ $t('booking.extra_guest_price') }}: {{ formatPrice(selectedService.extra_guest_price) }} {{ $t('booking.per_guest') }}
      </p>
    </div>

    <!-- Address Selection -->
    <div>
      <label for="address_id" class="block text-sm font-medium text-gray-700">
        {{ $t('booking.fields.address') }}
      </label>
      <select
        id="address_id"
        v-model="form.address_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-red-300': errors.address_id }"
      >
        <option value="">{{ $t('booking.select_address') }}</option>
        <option v-for="address in addresses" :key="address.id" :value="address.id">
          {{ address.address }}
        </option>
      </select>
      <p v-if="errors.address_id" class="mt-1 text-sm text-red-600">{{ errors.address_id }}</p>
    </div>

    <!-- Notes -->
    <div>
      <label for="notes" class="block text-sm font-medium text-gray-700">
        {{ $t('booking.fields.notes') }}
      </label>
      <textarea
        id="notes"
        v-model="form.notes"
        rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-red-300': errors.notes }"
        :placeholder="$t('booking.notes_placeholder')"
      ></textarea>
      <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</p>
    </div>

    <!-- Pricing Summary -->
    <div v-if="form.total_amount > 0" class="bg-gray-50 p-4 rounded-lg">
      <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('booking.pricing_summary') }}</h3>
      <div class="space-y-2">
        <div class="flex justify-between">
          <span>{{ $t('booking.base_price') }}:</span>
          <span>{{ formatPrice(form.unit_price * (selectedService?.service_type === 'hourly' ? form.hours_count : 1)) }}</span>
        </div>
        <div v-if="form.extra_guests_amount > 0" class="flex justify-between">
          <span>{{ $t('booking.extra_guests') }} ({{ form.extra_guests_count }}):</span>
          <span>{{ formatPrice(form.extra_guests_amount) }}</span>
        </div>
        <div class="border-t pt-2 flex justify-between font-semibold">
          <span>{{ $t('booking.total_amount') }}:</span>
          <span>{{ formatPrice(form.total_amount) }}</span>
        </div>
      </div>
    </div>

    <!-- Availability Check -->
    <ChefAvailability
      v-if="form.chef_id && form.date && form.start_time && form.hours_count"
      :chef-id="form.chef_id"
      :date="form.date"
      :start-time="form.start_time"
      :hours-count="form.hours_count"
      :exclude-booking-id="excludeBookingId"
      @availability-checked="onAvailabilityChecked"
    />

    <!-- Conflict Warning -->
    <ConflictWarning
      v-if="conflictingBookings.length > 0"
      :conflicting-bookings="conflictingBookings"
    />

    <!-- Submit Button -->
    <div class="flex justify-end space-x-3">
      <button
        type="button"
        @click="$emit('cancel')"
        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        {{ $t('common.cancel') }}
      </button>
      <button
        type="submit"
        :disabled="!isFormValid || isSubmitting"
        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="isSubmitting" class="inline-flex items-center">
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ $t('common.processing') }}
        </span>
        <span v-else>
          {{ isEditing ? $t('common.update') : $t('common.create') }}
        </span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import ChefAvailability from './ChefAvailability.vue'
import ConflictWarning from './ConflictWarning.vue'

const { t } = useI18n()

// Props
const props = defineProps({
  initialData: {
    type: Object,
    default: () => ({})
  },
  chefs: {
    type: Array,
    default: () => []
  },
  addresses: {
    type: Array,
    default: () => []
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  excludeBookingId: {
    type: Number,
    default: null
  }
})

// Emits
const emit = defineEmits(['submit', 'cancel'])

// Reactive data
const form = ref({
  chef_id: '',
  chef_service_id: '',
  address_id: '',
  date: '',
  start_time: '',
  hours_count: 1,
  number_of_guests: 1,
  service_type: 'hourly',
  unit_price: 0,
  extra_guests_count: 0,
  extra_guests_amount: 0,
  total_amount: 0,
  commission_amount: 0,
  notes: '',
  ...props.initialData
})

const errors = ref({})
const isSubmitting = ref(false)
const availableServices = ref([])
const selectedService = ref(null)
const conflictingBookings = ref([])
const isAvailable = ref(true)

// Computed properties
const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const maxDate = computed(() => {
  const maxDate = new Date()
  maxDate.setDate(maxDate.getDate() + 90)
  return maxDate.toISOString().split('T')[0]
})

const isFormValid = computed(() => {
  return form.value.chef_id &&
         form.value.chef_service_id &&
         form.value.date &&
         form.value.start_time &&
         form.value.hours_count &&
         form.value.number_of_guests &&
         form.value.total_amount > 0 &&
         isAvailable.value &&
         conflictingBookings.value.length === 0
})

// Methods
const onChefChange = async () => {
  form.value.chef_service_id = ''
  selectedService.value = null
  availableServices.value = []
  
  if (form.value.chef_id) {
    try {
      const response = await axios.get(`/api/chefs/${form.value.chef_id}/services`)
      availableServices.value = response.data.data
    } catch (error) {
      console.error('Error fetching chef services:', error)
    }
  }
}

const onServiceChange = () => {
  selectedService.value = availableServices.value.find(s => s.id == form.value.chef_service_id)
  if (selectedService.value) {
    form.value.service_type = selectedService.value.service_type
    form.value.unit_price = selectedService.value.service_type === 'hourly' 
      ? selectedService.value.hourly_rate 
      : selectedService.value.package_price
    calculatePricing()
  }
}

const onDateTimeChange = () => {
  // Reset availability when date/time changes
  isAvailable.value = true
  conflictingBookings.value = []
}

const onHoursChange = () => {
  calculatePricing()
  onDateTimeChange()
}

const calculateEndTime = () => {
  if (!form.value.start_time || !form.value.hours_count) return ''
  
  const [hours, minutes] = form.value.start_time.split(':').map(Number)
  const endTime = new Date()
  endTime.setHours(hours + form.value.hours_count, minutes)
  
  return endTime.toTimeString().slice(0, 5)
}

const calculatePricing = () => {
  if (!selectedService.value) return

  let basePrice = form.value.unit_price
  if (selectedService.value.service_type === 'hourly') {
    basePrice *= form.value.hours_count
  }

  let extraGuestsAmount = 0
  if (form.value.extra_guests_count > 0 && selectedService.value.allow_extra_guests) {
    extraGuestsAmount = form.value.extra_guests_count * selectedService.value.extra_guest_price
  }

  form.value.extra_guests_amount = extraGuestsAmount
  form.value.total_amount = basePrice + extraGuestsAmount
  form.value.commission_amount = form.value.total_amount * 0.1 // 10% commission
}

const onAvailabilityChecked = (result) => {
  isAvailable.value = result.available
  conflictingBookings.value = result.conflicting_bookings || []
  
  if (!result.available && result.errors) {
    errors.value.availability = result.errors.join(', ')
  } else {
    delete errors.value.availability
  }
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('ar-SA', {
    style: 'currency',
    currency: 'SAR'
  }).format(price)
}

const submitForm = async () => {
  if (!isFormValid.value) return

  isSubmitting.value = true
  errors.value = {}

  try {
    emit('submit', form.value)
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  } finally {
    isSubmitting.value = false
  }
}

// Initialize form
onMounted(() => {
  if (form.value.chef_id) {
    onChefChange()
  }
})

// Watch for service changes
watch(() => form.value.chef_service_id, () => {
  if (form.value.chef_service_id) {
    onServiceChange()
  }
})
</script>