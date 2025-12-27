<template>
  <div class="p-6">
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ t('landing_page.partners.title') }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ t('landing_page.partners.description') }}
          </p>
        </div>
        <button
          @click="addPartner"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('landing_page.common.add') }}
        </button>
      </div>

      <!-- Partners Grid -->
      <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div
          v-for="(partner, index) in partners"
          :key="index"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm"
        >
          <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-700 mb-4">
            <span class="text-sm font-bold text-gray-800 dark:text-white">
              {{ t('landing_page.partners.partner') }} #{{ index + 1 }}
            </span>
            <button
              @click="removePartner(index)"
              type="button"
              class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>

          <div class="space-y-3">
            <!-- Name Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.partners.name_ar') }}
              </label>
              <input 
                v-model="partner.name_ar" 
                type="text" 
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Name English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.partners.name_en') }}
              </label>
              <input 
                v-model="partner.name_en" 
                type="text" 
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>

            <!-- Logo URL -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.partners.logo') }}
              </label>
              <input 
                v-model="partner.logo" 
                type="text" 
                placeholder="https://..."
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Description Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.description_ar') }}
              </label>
              <textarea 
                v-model="partner.description_ar" 
                rows="2"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none" 
              ></textarea>
            </div>
            
            <!-- Description English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.description_en') }}
              </label>
              <textarea 
                v-model="partner.description_en" 
                rows="2"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none" 
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end">
        <button
          @click="savePartners"
          :disabled="saving"
          type="button"
          class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium disabled:opacity-50"
        >
          <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ saving ? t('landing_page.common.saving') : t('landing_page.common.save') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import { useNotifications } from '@/composables/useNotifications';

const { t } = useI18n();
const { success, error } = useNotifications();

const props = defineProps({
  section: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['refresh']);

const partners = ref(props.section?.additional_data?.partners || []);
const saving = ref(false);

const addPartner = () => {
  partners.value.push({
    name_ar: '',
    name_en: '',
    logo: '',
    description_ar: '',
    description_en: ''
  });
};

const removePartner = (index) => {
  if (confirm(t('landing_page.partners.confirm_delete'))) {
    partners.value.splice(index, 1);
  }
};

const savePartners = () => {
  saving.value = true;
  
  router.put(route('admin.landing-page-sections.update', props.section.id), {
    additional_data: {
      partners: partners.value,
      partnership_benefits: props.section?.additional_data?.partnership_benefits || []
    }
  }, {
    preserveScroll: true,
    onSuccess: () => {
      success(t('landing_page.messages.save_success'));
      emit('refresh');
    },
    onError: (errors) => {
      error(t('landing_page.messages.save_error'));
      console.error(errors);
    },
    onFinish: () => {
      saving.value = false;
    }
  });
};
</script>
