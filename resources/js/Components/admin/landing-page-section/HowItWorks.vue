<template>
  <div class="p-6">
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ t('landing_page.how_it_works.title') }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ t('landing_page.how_it_works.description') }}
          </p>
        </div>
        <button
          @click="addStep"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('landing_page.how_it_works.add_step') }}
        </button>
      </div>

      <!-- Steps List -->
      <div class="space-y-4">
        <div
          v-for="(step, index) in steps"
          :key="index"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm"
        >
          <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-700 mb-4">
            <span class="text-sm font-bold text-gray-800 dark:text-white">
              {{ t('landing_page.how_it_works.step') }} #{{ step.step }}
            </span>
            <button
              @click="removeStep(index)"
              type="button"
              class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <!-- Title Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.title_ar') }}
              </label>
              <input 
                v-model="step.title_ar" 
                type="text" 
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Title English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.title_en') }}
              </label>
              <input 
                v-model="step.title_en" 
                type="text" 
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>

            <!-- Description Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.description_ar') }}
              </label>
              <textarea 
                v-model="step.description_ar" 
                rows="3"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none" 
              ></textarea>
            </div>
            
            <!-- Description English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.description_en') }}
              </label>
              <textarea 
                v-model="step.description_en" 
                rows="3"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none" 
              ></textarea>
            </div>

            <!-- Icon -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.common.icon') }}
              </label>
              <IconPicker
                v-model="step.icon"
                :placeholder="t('landing_page.how_it_works.icon_placeholder')"
                :title="t('landing_page.how_it_works.select_icon')"
                :search-placeholder="t('landing_page.how_it_works.search_icon')"
                :no-icons-text="t('landing_page.how_it_works.no_icons_found')"
              />
            </div>

            <!-- Step Image -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('landing_page.how_it_works.step_image') }}
              </label>
              
              <!-- Image Preview -->
              <div v-if="step.imagePreview || step.image" class="mb-3">
                <div class="relative group rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-600 shadow-sm" style="max-width: 200px;">
                  <img 
                    :src="step.imagePreview || getImagePath(step.image)" 
                    class="w-full h-32 object-cover bg-gray-100 dark:bg-gray-800"
                    :alt="step.title_ar || step.title_en"
                    loading="lazy"
                  />
                  <button
                    @click="removeStepImage(index)"
                    class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-red-500 text-white rounded-full p-1.5 shadow-lg"
                    :title="t('common.delete')"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Upload Button -->
              <label class="cursor-pointer">
                <input
                  type="file"
                  @change="handleStepImageUpload($event, index)"
                  accept="image/*"
                  class="hidden"
                />
                <div class="btn-primary-outline inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                  </svg>
                  {{ step.image || step.imagePreview ? t('landing_page.hero.changeImage') : t('landing_page.hero.selectImage') }}
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="mt-6 flex justify-end">
        <button
          @click="saveSteps"
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
import IconPicker from './IconPicker.vue';

const { t } = useI18n();
const { success, error } = useNotifications();

const props = defineProps({
  section: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['refresh']);

const steps = ref((props.section?.additional_data?.steps || []).map(step => ({
  ...step,
  imagePreview: null,
  imageFile: null
})));
const saving = ref(false);

const addStep = () => {
  steps.value.push({
    step: steps.value.length + 1,
    title_ar: '',
    title_en: '',
    description_ar: '',
    description_en: '',
    icon: '',
    image: '',
    imagePreview: null,
    imageFile: null
  });
};

const removeStep = (index) => {
  if (confirm(t('landing_page.how_it_works.confirm_delete'))) {
    steps.value.splice(index, 1);
    // Re-number steps
    steps.value.forEach((step, idx) => {
      step.step = idx + 1;
    });
  }
};

const handleStepImageUpload = (event, index) => {
  const file = event.target.files?.[0];
  if (!file) return;

  // Validate file type
  if (!file.type.startsWith('image/')) {
    error(t('landing_page.messages.invalid_image'));
    return;
  }

  // Validate file size (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    error(t('landing_page.messages.image_too_large'));
    return;
  }

  // Create preview
  const reader = new FileReader();
  reader.onload = (e) => {
    steps.value[index].imagePreview = e.target.result;
  };
  reader.readAsDataURL(file);

  // Store file for upload
  steps.value[index].imageFile = file;
};

const removeStepImage = (index) => {
  steps.value[index].image = '';
  steps.value[index].imagePreview = null;
  steps.value[index].imageFile = null;
};

const getImagePath = (imagePath) => {
  if (!imagePath) return '';
  if (imagePath.startsWith('http')) return imagePath;
  return `/storage/${imagePath}`;
};

const saveSteps = () => {
  saving.value = true;
  
  // Prepare form data with images
  const formData = new FormData();
  formData.append('_method', 'PUT');
  formData.append('section_key', props.section.section_key || 'how_it_works');
  
  // Prepare steps data (without imageFile and imagePreview)
  const stepsData = steps.value.map((step, idx) => {
    const stepData = {
      step: step.step,
      title_ar: step.title_ar,
      title_en: step.title_en,
      description_ar: step.description_ar,
      description_en: step.description_en,
      icon: step.icon,
      image: step.image || ''
    };
    
    // Add image file if exists
    if (step.imageFile) {
      formData.append(`step_images[${idx}]`, step.imageFile);
    }
    
    return stepData;
  });
  
  formData.append('additional_data', JSON.stringify({ steps: stepsData }));

  router.post(route('admin.landing-page-sections.update', props.section.id), formData, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      success(t('landing_page.messages.save_success'));
      // Clear imageFile and imagePreview after successful save
      steps.value.forEach(step => {
        step.imageFile = null;
        if (step.imagePreview && !step.image) {
          step.imagePreview = null;
        }
      });
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
