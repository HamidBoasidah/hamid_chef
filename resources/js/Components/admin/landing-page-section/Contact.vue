<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          {{ t('landing_page.contact.title') }}
        </h3>

        <div class="space-y-4">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.contact.phone') }}
            </label>
            <input
              v-model="contactData.phone"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.contact.email') }}
            </label>
            <input
              v-model="contactData.email"
              type="email"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.contact.address_ar') }}
            </label>
            <input
              v-model="contactData.address_ar"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.contact.address_en') }}
            </label>
            <input
              v-model="contactData.address_en"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.contact.working_hours_ar') }}
            </label>
            <input
              v-model="contactData.working_hours_ar"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('landing_page.contact.working_hours_en') }}
            </label>
            <input
              v-model="contactData.working_hours_en"
              type="text"
              class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
          </div>

          <div class="pt-4">
            <button
              @click="saveContactData"
              :disabled="saving"
              class="btn-primary-outline inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition disabled:opacity-50"
            >
              {{ saving ? t('landing_page.common.saving') : t('landing_page.common.save') }}
            </button>
          </div>
        </div>
      </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useForm } from '@inertiajs/vue3'
import { useNotifications, extractErrorMessage } from '@/composables/useNotifications'

const { t } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({
  section: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['refresh'])

const contactData = ref({
  phone: '',
  email: '',
  address_ar: '',
  address_en: '',
  working_hours_ar: '',
  working_hours_en: '',
})

const saving = ref(false)

watch(
  () => props.section,
  (newSection) => {
    if (newSection && newSection.additional_data) {
      contactData.value = {
        phone: newSection.additional_data.phone || '',
        email: newSection.additional_data.email || '',
        address_ar: newSection.additional_data.address_ar || '',
        address_en: newSection.additional_data.address_en || '',
        working_hours_ar: newSection.additional_data.working_hours_ar || '',
        working_hours_en: newSection.additional_data.working_hours_en || '',
      }
    }
  },
  { immediate: true }
)

const saveContactData = () => {
  if (!props.section) {
    error(t('landing_page.messages.save_error'))
    return
  }

  saving.value = true

  const form = useForm({
    _method: 'PUT',
    section_key: props.section?.section_key || '',
    additional_data: JSON.stringify({
      ...props.section.additional_data,
      ...contactData.value,
    }),
  })

  form.post(route('admin.landing-page-sections.update', props.section.id), {
    onSuccess: () => {
      success(t('landing_page.messages.save_success'))
      saving.value = false
      emit('refresh')
    },
    onError: (errors) => {
      const message = extractErrorMessage(errors, t('landing_page.messages.save_error'))
      error(message)
      saving.value = false
    },
    preserveScroll: true,
    forceFormData: true,
  })
}
</script>
