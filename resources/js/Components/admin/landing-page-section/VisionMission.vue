<template>
  <div class="p-6">
    <!-- Vision / Mission / Values Section -->
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ t('landingSections.visionMissionValues') }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ t('landingSections.visionMissionDescription') }}
          </p>
        </div>
      </div>

      <div class="grid md:grid-cols-3 gap-6">
        <div
          v-for="(item, index) in items"
          :key="item.key || index"
          class="space-y-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm"
        >
          <!-- Item Header with Icon -->
          <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="getItemColor(item.key)">
              <component :is="getItemIcon(item.key)" class="w-5 h-5 text-white" />
            </div>
            <span class="text-base font-bold text-gray-800 dark:text-white">
              {{ getItemLabel(item.key) }}
            </span>
          </div>

          <div class="space-y-3">
            <!-- Title Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.titleAr') }}
              </label>
              <input 
                v-model="item.title_ar" 
                type="text" 
                :placeholder="getItemPlaceholder(item.key, 'title', 'ar')"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Title English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.titleEn') }}
              </label>
              <input 
                v-model="item.title_en" 
                type="text" 
                :placeholder="getItemPlaceholder(item.key, 'title', 'en')"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Description Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.descriptionAr') }}
              </label>
              <textarea 
                v-model="item.description_ar" 
                rows="3" 
                :placeholder="getItemPlaceholder(item.key, 'description', 'ar')"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"
              ></textarea>
            </div>
            
            <!-- Description English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.descriptionEn') }}
              </label>
              <textarea 
                v-model="item.description_en" 
                rows="3" 
                :placeholder="getItemPlaceholder(item.key, 'description', 'en')"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
        <button
          type="button"
          :disabled="saving"
          @click="saveItems"
          class="btn-primary inline-flex items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-medium transition disabled:opacity-50"
        >
          <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ saving ? t('common.saving') : t('common.saveChanges') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useNotifications, extractErrorMessage } from '@/composables/useNotifications'

const { t } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({
  section: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['refresh'])

const saving = ref(false)

const defaultItems = () => ([
  { key: 'vision', title_ar: '', title_en: '', description_ar: '', description_en: '' },
  { key: 'mission', title_ar: '', title_en: '', description_ar: '', description_en: '' },
  { key: 'values', title_ar: '', title_en: '', description_ar: '', description_en: '' },
])

const items = ref(defaultItems())

// Icons for each item
const VisionIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>' }
const MissionIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>' }
const ValuesIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>' }

const getItemIcon = (key) => {
  const icons = { vision: VisionIcon, mission: MissionIcon, values: ValuesIcon }
  return icons[key] || VisionIcon
}

const getItemColor = (key) => {
  const colors = {
    vision: 'bg-blue-500',
    mission: 'bg-green-500',
    values: 'bg-purple-500'
  }
  return colors[key] || 'bg-gray-500'
}

const getItemLabel = (key) => {
  const labels = {
    vision: t('landingSections.vision'),
    mission: t('landingSections.mission'),
    values: t('landingSections.values')
  }
  return labels[key] || key
}

const getItemPlaceholder = (key, field, lang) => {
  const placeholders = {
    vision: {
      title: { ar: 'أدخل عنوان الرؤية', en: 'Enter vision title' },
      description: { ar: 'أدخل وصف الرؤية', en: 'Enter vision description' }
    },
    mission: {
      title: { ar: 'أدخل عنوان الرسالة', en: 'Enter mission title' },
      description: { ar: 'أدخل وصف الرسالة', en: 'Enter mission description' }
    },
    values: {
      title: { ar: 'أدخل عنوان القيم', en: 'Enter values title' },
      description: { ar: 'أدخل وصف القيم', en: 'Enter values description' }
    }
  }
  return placeholders[key]?.[field]?.[lang] || ''
}

// Load items from section data
watch(() => props.section, (newSection) => {
  if (newSection?.additional_data?.items) {
    items.value = JSON.parse(JSON.stringify(newSection.additional_data.items))
  } else {
    items.value = defaultItems()
  }
}, { immediate: true })

// Save items
const saveItems = () => {
  if (!props.section || !props.section.id) {
    error(t('landingSections.sectionNotFound'))
    return
  }

  saving.value = true

  const form = useForm({
    _method: 'PUT',
    section_key: props.section.section_key || 'vision_mission',
    additional_data: JSON.stringify({ items: items.value })
  })

  form.post(route('admin.landing-page-sections.update', props.section.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      success(t('landingSections.sectionUpdated'))
      saving.value = false
      emit('refresh')
    },
    onError: (errors) => {
      const message = extractErrorMessage(errors, t('landingSections.updateFailed'))
      error(message)
      saving.value = false
    }
  })
}
</script>
