<template>
  <div class="p-6 space-y-8">
    <!-- Reasons Section -->
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ t('landing_page.why_us.reasons_title') }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ t('landing_page.why_us.reasons_description') }}
          </p>
        </div>
        <button
          @click="addReason"
          class="btn-primary-outline inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('landing_page.why_us.add_reason') }}
        </button>
      </div>

      <!-- Reasons Grid -->
      <div v-if="reasons.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ t('landing_page.why_us.no_reasons') }}</h3>
        <p class="text-gray-500 dark:text-gray-400">{{ t('landing_page.why_us.add_first_reason') }}</p>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div
          v-for="(reason, index) in reasons"
          :key="index"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm relative group"
        >
          <!-- Delete Button -->
          <button
            @click="removeReason(index)"
            class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity p-1.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50"
            :title="t('common.delete')"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>

          <!-- Icon Selector -->
          <div class="mb-4">
            <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
              {{ t('landing_page.common.icon') }}
            </label>
            <IconPicker
              v-model="reason.icon"
              :placeholder="t('landing_page.why_us.select_icon')"
              :title="t('landing_page.why_us.select_icon')"
              :search-placeholder="t('landing_page.why_us.search_icon')"
              :no-icons-text="t('landing_page.why_us.no_icons_found')"
            />
          </div>

          <div class="space-y-3">
            <!-- Title Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.title_ar') }}
              </label>
              <input 
                v-model="reason.title_ar" 
                type="text" 
                :placeholder="t('landing_page.why_us.title_ar_placeholder')"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Title English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.title_en') }}
              </label>
              <input 
                v-model="reason.title_en" 
                type="text" 
                :placeholder="t('landing_page.why_us.title_en_placeholder')"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Description Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.description_ar') }}
              </label>
              <textarea 
                v-model="reason.description_ar" 
                rows="2" 
                :placeholder="t('landing_page.why_us.description_ar_placeholder')"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"
              ></textarea>
            </div>
            
            <!-- Description English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.description_en') }}
              </label>
              <textarea 
                v-model="reason.description_en" 
                rows="2" 
                :placeholder="t('landing_page.why_us.description_en_placeholder')"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"
              ></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ t('landing_page.why_us.stats_title') }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ t('landing_page.why_us.stats_description') }}
          </p>
        </div>
        <button
          @click="addStat"
          class="btn-primary-outline inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('landing_page.why_us.add_stat') }}
        </button>
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        <div
          v-for="(stat, index) in stats"
          :key="index"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm relative group"
        >
          <!-- Delete Button -->
          <button
            @click="removeStat(index)"
            class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity p-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50"
            :title="t('common.delete')"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="space-y-2">
            <!-- Number -->
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.why_us.stat_number') }}
              </label>
              <input 
                v-model="stat.number" 
                type="text" 
                :placeholder="t('landing_page.why_us.stat_number_placeholder')"
                class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Label Arabic -->
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.why_us.stat_label_ar') }}
              </label>
              <input 
                v-model="stat.label_ar" 
                type="text" 
                :placeholder="t('landing_page.why_us.stat_label_ar_placeholder')"
                class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Label English -->
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.why_us.stat_label_en') }}
              </label>
              <input 
                v-model="stat.label_en" 
                type="text" 
                :placeholder="t('landing_page.why_us.stat_label_en_placeholder')"
                class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
      <button
        type="button"
        :disabled="saving"
        @click="saveFeatures"
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
</template>

<script setup>
import { watch, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useNotifications, extractErrorMessage } from '@/composables/useNotifications'
import IconPicker from './IconPicker.vue'

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

const defaultReason = () => ({
  icon: 'Certificate',
  title_ar: '',
  title_en: '',
  description_ar: '',
  description_en: ''
})

const defaultStat = () => ({
  number: '',
  label_ar: '',
  label_en: ''
})

const reasons = ref([])
const stats = ref([])

// Load data from section
watch(() => props.section, (newSection) => {
  if (newSection?.additional_data?.reasons) {
    reasons.value = JSON.parse(JSON.stringify(newSection.additional_data.reasons))
  } else {
    reasons.value = []
  }
  
  if (newSection?.additional_data?.stats) {
    stats.value = JSON.parse(JSON.stringify(newSection.additional_data.stats))
  } else {
    stats.value = []
  }
}, { immediate: true })

const addReason = () => {
  reasons.value.push(defaultReason())
}

const removeReason = (index) => {
  if (confirm(t('common.confirmDelete'))) {
    reasons.value.splice(index, 1)
  }
}

const addStat = () => {
  stats.value.push(defaultStat())
}

const removeStat = (index) => {
  if (confirm(t('common.confirmDelete'))) {
    stats.value.splice(index, 1)
  }
}

// Save all data
const saveFeatures = () => {
  if (!props.section || !props.section.id) {
    error(t('landing_page.messages.save_error'))
    return
  }

  const dataToSave = { 
    reasons: reasons.value,
    stats: stats.value
  }

  saving.value = true

  const form = useForm({
    _method: 'PUT',
    section_key: props.section.section_key || 'why_us',
    additional_data: JSON.stringify(dataToSave)
  })

  form.post(route('admin.landing-page-sections.update', props.section.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      success(t('landing_page.messages.save_success'))
      saving.value = false
      window.location.reload()
    },
    onError: (errors) => {
      const message = extractErrorMessage(errors, t('landing_page.messages.save_error'))
      error(message)
      saving.value = false
    }
  })
}
</script>
