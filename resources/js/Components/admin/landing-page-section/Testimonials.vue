<template>
  <div class="p-6">
    <!-- Testimonials Section -->
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ t('landingSections.testimonials', 'آراء العملاء') }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ t('landingSections.testimonialsDescription', 'إدارة آراء العملاء المعروضة في الصفحة الرئيسية') }}
          </p>
        </div>
        <button
          @click="addTestimonial"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('common.add', 'إضافة') }}
        </button>
      </div>

      <!-- Testimonials Grid -->
      <div class="grid md:grid-cols-2 gap-6">
        <div
          v-for="(testimonial, index) in testimonials"
          :key="index"
          class="space-y-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm"
        >
          <!-- Header with Delete -->
          <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-700">
            <span class="text-sm font-bold text-gray-800 dark:text-white">
              {{ t('landingSections.testimonialNumber', 'رأي') }} #{{ index + 1 }}
            </span>
            <button
              @click="removeTestimonial(index)"
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
                {{ t('common.nameAr', 'الاسم (عربي)') }}
              </label>
              <input 
                v-model="testimonial.name_ar" 
                type="text" 
                placeholder="د. أحمد محمد"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Name English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.nameEn', 'الاسم (إنجليزي)') }}
              </label>
              <input 
                v-model="testimonial.name_en" 
                type="text" 
                placeholder="Dr. Ahmed Mohammed"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Role Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.roleAr', 'المنصب (عربي)') }}
              </label>
              <input 
                v-model="testimonial.role_ar" 
                type="text" 
                placeholder="مدير مستشفى"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Role English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.roleEn', 'المنصب (إنجليزي)') }}
              </label>
              <input 
                v-model="testimonial.role_en" 
                type="text" 
                placeholder="Hospital Director"
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
            
            <!-- Content Arabic -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.contentAr', 'المحتوى (عربي)') }}
              </label>
              <textarea 
                v-model="testimonial.content_ar" 
                rows="3" 
                placeholder="منصة رائعة..."
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"
              ></textarea>
            </div>
            
            <!-- Content English -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.contentEn', 'المحتوى (إنجليزي)') }}
              </label>
              <textarea 
                v-model="testimonial.content_en" 
                rows="3" 
                placeholder="Amazing platform..."
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"
              ></textarea>
            </div>
            
            <!-- Avatar URL -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">
                {{ t('common.avatarUrl', 'رابط الصورة') }}
              </label>
              <input 
                v-model="testimonial.avatar" 
                type="text" 
                placeholder="https://..."
                class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" 
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="testimonials.length === 0"
        class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600"
      >
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <p class="text-gray-600 dark:text-white mb-4">
          {{ t('landingSections.noTestimonials', 'لا توجد آراء بعد') }}
        </p>
        <button
          @click="addTestimonial"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('landingSections.addFirstTestimonial', 'إضافة أول رأي') }}
        </button>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
        <button
          type="button"
          :disabled="saving"
          @click="saveTestimonials"
          class="btn-primary inline-flex items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-medium transition disabled:opacity-50"
        >
          <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ saving ? t('common.saving') : t('common.save') }}
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
    required: true
  }
})

const emit = defineEmits(['refresh'])

const testimonials = ref([])
const saving = ref(false)

// Load testimonials from section
watch(() => props.section, (newSection) => {
  if (newSection?.additional_data?.items) {
    testimonials.value = JSON.parse(JSON.stringify(newSection.additional_data.items))
  } else {
    testimonials.value = []
  }
}, { immediate: true })

const addTestimonial = () => {
  testimonials.value.push({
    name_ar: '',
    name_en: '',
    role_ar: '',
    role_en: '',
    content_ar: '',
    content_en: '',
    avatar: ''
  })
}

const removeTestimonial = (index) => {
  if (confirm(t('common.confirmDelete', 'هل أنت متأكد من الحذف؟'))) {
    testimonials.value.splice(index, 1)
  }
}

const saveTestimonials = () => {
  if (!props.section || !props.section.id) {
    error(t('landingSections.sectionNotFound', 'القسم غير موجود'))
    return
  }

  saving.value = true

  const form = useForm({
    _method: 'PUT',
    section_key: props.section.section_key || 'testimonials',
    additional_data: JSON.stringify({ items: testimonials.value })
  })

  form.post(route('admin.landing-page-sections.update', props.section.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      success(t('common.savedSuccessfully', 'تم الحفظ بنجاح'))
      saving.value = false
      emit('refresh')
    },
    onError: (errors) => {
      const message = extractErrorMessage(errors, t('common.saveFailed', 'فشل الحفظ'))
      error(message)
      saving.value = false
    }
  })
}
</script>
