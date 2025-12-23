<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-medium text-gray-800 dark:text-white">
        {{ $t('equipment.equipment_management') }}
      </h3>
      <button
        @click="addEquipment"
        type="button"
        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        {{ $t('equipment.add_equipment') }}
      </button>
    </div>

    <div v-if="equipment.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <p class="mt-2">{{ $t('equipment.no_equipment') }}</p>
      <p class="text-sm text-gray-400">{{ $t('equipment.add_equipment') }}</p>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="(item, index) in equipment"
        :key="index"
        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50"
      >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Equipment Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              {{ $t('equipment.name') }} *
            </label>
            <input
              v-model="item.name"
              type="text"
              :placeholder="$t('equipment.equipment_name_placeholder')"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              :class="{ 'border-red-500': item.errors?.name }"
            />
            <p v-if="item.errors?.name" class="mt-1 text-sm text-red-600">{{ item.errors.name }}</p>
          </div>

          <!-- Equipment Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              {{ $t('equipment.equipment_type') }} *
            </label>
            <select
              v-model="item.is_included"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option :value="true">{{ $t('equipment.included') }}</option>
              <option :value="false">{{ $t('equipment.client_provided') }}</option>
            </select>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end">
            <button
              @click="removeEquipment(index)"
              type="button"
              class="inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded text-red-600 hover:text-red-800 focus:outline-none"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              {{ $t('equipment.delete_equipment') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Equipment Summary -->
    <div v-if="equipment.length > 0" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
      <div class="flex items-center text-sm text-blue-800 dark:text-blue-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>
          {{ $t('equipment.equipment_list') }}: {{ equipment.length }} {{ $t('equipment.name').toLowerCase() }}
          ({{ includedCount }} {{ $t('equipment.included').toLowerCase() }}, {{ clientProvidedCount }} {{ $t('equipment.client_provided').toLowerCase() }})
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])

const equipment = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const includedCount = computed(() => 
  equipment.value.filter(item => item.is_included === true).length
)

const clientProvidedCount = computed(() => 
  equipment.value.filter(item => item.is_included === false).length
)

const addEquipment = () => {
  const newEquipment = {
    name: '',
    is_included: true,
    errors: {}
  }
  equipment.value = [...equipment.value, newEquipment]
}

const removeEquipment = (index) => {
  equipment.value = equipment.value.filter((_, i) => i !== index)
}

// Watch for changes and emit to parent
watch(equipment, (newValue) => {
  emit('update:modelValue', newValue)
}, { deep: true })
</script>