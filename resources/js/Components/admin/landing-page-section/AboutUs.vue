<template>
  <div class="p-6 space-y-8">
    <!-- Basic Info Section -->
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        {{ t('landing_page.about_us.basic_info') }}
      </h3>
      
      <form @submit.prevent="saveBasicInfo" class="space-y-4">
        <!-- Title -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.common.title_ar') }}
            </label>
            <input
              v-model="form.title_ar"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.common.title_en') }}
            </label>
            <input
              v-model="form.title_en"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>
        </div>

        <!-- Subtitle -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.about_us.subtitle_ar') }}
            </label>
            <input
              v-model="form.subtitle_ar"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.about_us.subtitle_en') }}
            </label>
            <input
              v-model="form.subtitle_en"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>
        </div>

        <!-- Description -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.common.description_ar') }}
            </label>
            <textarea
              v-model="form.description_ar"
              rows="4"
              class="w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-none"
            ></textarea>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.common.description_en') }}
            </label>
            <textarea
              v-model="form.description_en"
              rows="4"
              class="w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-hidden focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-none"
            ></textarea>
          </div>
        </div>

        <!-- Image Upload -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ t('landing_page.common.image') }}
          </label>
          <ImageUploadBox
            v-model="form.image"
            :initial-image="section.image_url || null"
            :label="t('landing_page.about_us.about_image')"
            input-id="about-image"
          />
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-2">
          <button
            type="submit"
            :disabled="form.processing"
            class="btn-primary-outline  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition inline-flex items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-medium transition disabled:opacity-50"
          >
            <svg v-if="!form.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ form.processing ? t('landing_page.common.save') : t('landing_page.common.save') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Vision / Mission / Values Combined (shown only for vision_mission section) -->
    <div v-if="isVisionMission" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        {{ t('landing_page.about_us.vision_mission_values') }}
      </h3>

      <div class="grid md:grid-cols-3 gap-4">
        <div
          v-for="(item, index) in items"
          :key="item.key || index"
          class="space-y-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"
        >
          <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700 dark:text-white">
              {{ item.key ? item.key.toUpperCase() : t('common.item') }}
            </span>
          </div>

          <div class="space-y-2">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ t('landing_page.common.title') }} ({{ t('landing_page.common.title_ar') }})</label>
              <input v-model="item.title_ar" type="text" class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white" />
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ t('landing_page.common.title') }} ({{ t('landing_page.common.title_en') }})</label>
              <input v-model="item.title_en" type="text" class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-gray-800 dark:text-white" />
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ t('landing_page.common.description') }} ({{ t('landing_page.common.description_ar') }})</label>
              <textarea v-model="item.description_ar" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-2 text-sm text-gray-800 dark:text-white"></textarea>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ t('landing_page.common.description') }} ({{ t('landing_page.common.description_en') }})</label>
              <textarea v-model="item.description_en" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-2 text-sm text-gray-800 dark:text-white"></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end pt-4">
        <button
          type="button"
          :disabled="form.processing"
          @click="saveBasicInfo"
          class="btn-primary-outline inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition disabled:opacity-50"
        >
          <svg v-if="!form.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ form.processing ? t('landing_page.common.save') : t('landing_page.common.save') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch, ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useNotifications, extractErrorMessage } from '@/composables/useNotifications'
import ImageUploadBox from '@/Components/common/ImageUploadBox.vue'

const { t } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({
  section: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['refresh'])

const resolvedSectionKey = computed(() => props.section?.section_key || 'about_us')
const isVisionMission = computed(() => props.section?.section_key === 'vision_mission')

const defaultItems = () => ([
  { key: 'vision', title_ar: '', title_en: '', description_ar: '', description_en: '' },
  { key: 'mission', title_ar: '', title_en: '', description_ar: '', description_en: '' },
  { key: 'values', title_ar: '', title_en: '', description_ar: '', description_en: '' },
])

const items = ref(isVisionMission.value ? defaultItems() : [])

// Basic info form
const form = useForm({
  _method: 'PUT',
  section_key: resolvedSectionKey.value,
  title_ar: props.section.title_ar || '',
  title_en: props.section.title_en || '',
  subtitle_ar: props.section.subtitle_ar || '',
  subtitle_en: props.section.subtitle_en || '',
  description_ar: props.section.description_ar || '',
  description_en: props.section.description_en || '',
  image: null,
  additional_data: null // Will be JSON stringified before submission
})

// Update form when section changes
watch(() => props.section, (newSection) => {
  console.log('🔄 Section prop changed:', {
    id: newSection.id,
    title_ar: newSection.title_ar,
    title_en: newSection.title_en,
    updated_at: newSection.updated_at
  })
  
  form.section_key = newSection.section_key || resolvedSectionKey.value
  form.title_ar = newSection.title_ar || ''
  form.title_en = newSection.title_en || ''
  form.subtitle_ar = newSection.subtitle_ar || ''
  form.subtitle_en = newSection.subtitle_en || ''
  form.description_ar = newSection.description_ar || ''
  form.description_en = newSection.description_en || ''
  
  // Load items for vision/mission section (stored in reactive ref, not in form)
  if (isVisionMission.value) {
    const sectionItems = newSection.additional_data?.items
    items.value = Array.isArray(sectionItems) && sectionItems.length ? JSON.parse(JSON.stringify(sectionItems)) : defaultItems()
  } else {
    items.value = []
  }
  // Note: form.additional_data will be JSON stringified at submission time
}, { immediate: true })

// Save basic info
const saveBasicInfo = () => {
  console.log('🔄 AboutTab: Starting form submission...')

  if (!props.section || !props.section.id) {
    console.error('❌ No section or section ID found')
    error('Section not found')
    return
  }

  // Make sure section_key is always set before submit
  form.section_key = resolvedSectionKey.value

  // CRITICAL FIX: When using forceFormData (for file uploads), objects get serialized as [object Object]
  // We must JSON stringify additional_data before submission
  if (isVisionMission.value) {
    // Update items from the reactive ref before submission
    form.additional_data = JSON.stringify({ items: items.value })
  } else {
    form.additional_data = null
  }

  console.log('🚀 Submitting to route:', route('admin.landing-page-sections.update', props.section.id))
  console.log('📋 additional_data:', form.additional_data)
  
  form.post(route('admin.landing-page-sections.update', props.section.id), {
    _method: 'PUT',
    forceFormData: true,
    preserveScroll: true,
    onSuccess: (response) => {
      console.log('✅ AboutTab: Form submitted successfully')
      success(t('landing_page.about_us.section_updated'))
      form.image = null
      emit('refresh')
    },
    onError: (errors) => {
      console.error('❌ AboutTab: Form submission failed', errors)
      const message = extractErrorMessage(errors, t('landing_page.about_us.update_failed'))
      error(message)
    }
  })
}
</script>
