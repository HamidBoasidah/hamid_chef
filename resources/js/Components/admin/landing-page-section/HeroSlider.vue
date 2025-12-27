<template>
  <!-- Hero Slides Management -->
  <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-50 to-secondary-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 border border-primary-200 dark:border-gray-600">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ t('landing_page.hero.title') }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-white mt-1">
                {{ t('landing_page.hero.description') }}
              </p>
            </div>
            <button
              @click="addSlide"
              class="btn-primary-outline  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition inline-flex items-center justify-center gap-2 rounded-lg px-4 py-[11px] text-sm font-medium transition"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              {{ t('landing_page.hero.addSlide') }}
            </button>
          </div>
        </div>

        <!-- Slides List -->
        <div v-if="slides.length === 0" class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ t('landing_page.hero.noSlides') }}</h3>
          <p class="text-gray-500 dark:text-gray-400">{{ t('landing_page.hero.noSlidesDescription') }}</p>
        </div>

        <draggable 
          v-model="slides" 
          @end="updateSlideOrder"
          item-key="id"
          class="space-y-4"
          handle=".drag-handle"
        >
          <template #item="{element: slide, index}">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden transition-all hover:shadow-lg">
              <!-- Slide Header -->
              <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center gap-3">
                  <button class="drag-handle cursor-move p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                    </svg>
                  </button>
                  <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">
                      {{ slide.title_ar || slide.title_en || t('landing_page.hero.slideNumber', {number: index + 1}) }}
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ t('landing_page.hero.order') }}: {{ index + 1 }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <button
                    @click="toggleSlideEdit(index)"
                    class="btn-warning-outline"
                    :title="t('common.edit')"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="removeSlide(index)"
                    class="btn-danger-outline"
                    :title="t('common.delete')"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Slide Content (Collapsible) -->
              <div v-show="slide.isEditing" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Title AR -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('common.title') }} ({{ t('common.arabic') }})
                    </label>
                    <input
                      v-model="slide.title_ar"
                      type="text"
                      class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                      :placeholder="t('landing_page.hero.titlePlaceholder')"
                    />
                  </div>

                  <!-- Title EN -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('common.title') }} ({{ t('common.english') }})
                    </label>
                    <input
                      v-model="slide.title_en"
                      type="text"
                      class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                      :placeholder="t('landing_page.hero.titlePlaceholder')"
                    />
                  </div>

                  <!-- Subtitle AR -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('common.subtitle') }} ({{ t('common.arabic') }})
                    </label>
                    <input
                      v-model="slide.subtitle_ar"
                      type="text"
                      class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    />
                  </div>

                  <!-- Subtitle EN -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('common.subtitle') }} ({{ t('common.english') }})
                    </label>
                    <input
                      v-model="slide.subtitle_en"
                      type="text"
                      class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    />
                  </div>
                </div>

                <!-- Description -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('common.description') }} ({{ t('common.arabic') }})
                    </label>
                    <textarea
                      v-model="slide.description_ar"
                      rows="3"
                      class="w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none"
                    ></textarea>
                  </div>
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('common.description') }} ({{ t('common.english') }})
                    </label>
                    <textarea
                      v-model="slide.description_en"
                      rows="3"
                      class="w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none"
                    ></textarea>
                  </div>
                </div>

                <!-- Image Upload -->
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ t('landing_page.hero.slideImage') }}
                    <span class="text-xs text-gray-500 dark:text-white ml-2">({{ t('landing_page.hero.recommendedSize') }}: 1920x1080)</span>
                  </label>
                  
                  <!-- Image Preview Area -->
                  <div v-if="slide.imagePreview || (slide.image && slide.image !== 'null' && slide.image !== null && slide.image.trim() !== '')" class="mb-4">
                    <div class="relative group rounded-xl overflow-hidden border-2 border-gray-200 dark:border-gray-600 shadow-lg hover:shadow-xl transition-all duration-300" style="aspect-ratio: 16/9; max-width: 300px;">
                      <img 
                        :src="slide.imagePreview || getImagePath(slide.image)" 
                        class="w-full h-full object-cover bg-gray-100 dark:bg-gray-800"
                        :alt="slide.title_ar || slide.title_en"
                        loading="lazy"
                        @error="handleImageError"
                        @load="handleImageLoad"
                      />
                      <!-- Overlay on hover -->
                      <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                        <button
                          @click="removeSlideImage(index)"
                          class="opacity-0 group-hover:opacity-100 transform scale-90 group-hover:scale-100 transition-all duration-300 btn-danger-outline rounded-full p-3"
                          :title="t('common.delete')"
                        >
                          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                      <!-- Image badge -->
                      <div class="absolute top-2 left-2">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full text-xs font-medium text-gray-700 dark:text-white shadow-md">
                          <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                          </svg>
                          {{ slide.imagePreview ? t('common.pending') : t('common.saved') }}
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Upload Button -->
                  <div class="flex items-center gap-3">
                    <label class="cursor-pointer">
                      <input
                        type="file"
                        @change="handleSlideImageUpload($event, index)"
                        accept="image/*"
                        class="hidden"
                      />
                      <div class="btn-primary-outline  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition inline-flex items-center justify-center gap-2 rounded-lg px-4 py-[11px] text-sm font-medium transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ slide.image || slide.imagePreview ? t('landing_page.hero.changeImage') : t('landing_page.hero.selectImage') }}
                      </div>
                    </label>
                    
                    <div v-if="!slide.image && !slide.imagePreview" class="text-sm text-gray-500 dark:text-gray-400">
                      {{ t('landing_page.hero.noImageSelected') }}
                    </div>
                  </div>
                </div>

                <!-- Button Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('landing_page.hero.buttonText') }} (AR)
                    </label>
                    <input
                      v-model="slide.button_text_ar"
                      type="text"
                      class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    />
                  </div>
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('landing_page.hero.buttonText') }} (EN)
                    </label>
                    <input
                      v-model="slide.button_text_en"
                      type="text"
                      class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    />
                  </div>
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      {{ t('landing_page.hero.buttonUrl') }}
                    </label>
                    <input
                      v-model="slide.button_url"
                      type="text"
                      class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                      placeholder="https://..."
                    />
                  </div>
                </div>
              </div>

              <!-- Slide Preview (Collapsed State) -->
              <div v-if="!slide.isEditing" class="p-6 bg-gray-50 dark:bg-gray-900">
                <div v-if="slide.image && slide.image !== 'null' && slide.image !== null && slide.image.trim() !== ''" class="relative group rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300" style="aspect-ratio: 16/9; max-width: 300px;">
                  <img 
                    :src="getImagePath(slide.image)" 
                    class="w-full h-full object-cover bg-gray-100 dark:bg-gray-800"
                    :alt="slide.title_ar || slide.title_en"
                    loading="lazy"
                    @error="handleImageError"
                    @load="handleImageLoad"
                  />
                  <!-- Preview Overlay -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end p-3">
                    <div class="text-white">
                      <h4 class="text-sm font-bold mb-0.5">{{ slide.title_ar || slide.title_en }}</h4>
                      <p v-if="slide.subtitle_ar || slide.subtitle_en" class="text-xs opacity-90">{{ slide.subtitle_ar || slide.subtitle_en }}</p>
                    </div>
                  </div>
                </div>
                <div v-else class="flex items-center justify-center h-32 bg-gray-100 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600" style="max-width: 300px;">
                  <div class="text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ t('landing_page.hero.noImage') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </draggable>

        <!-- Save Button -->
        <div v-if="hasChanges" class="sticky bottom-4 z-10">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border-2 border-primary-500 p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-700 dark:text-white">
                  {{ t('landing_page.hero.unsavedChanges') }}
                </span>
              </div>
              <div class="flex gap-2">
                <button
                  @click="resetChanges"
                  class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                >
                  {{ t('common.cancel') }}
                </button>
                <button
                  @click="saveSlides"
                  :disabled="saving"
                  class="btn-primary-outline  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition inline-flex items-center justify-center gap-2 rounded-lg px-4 py-[11px] text-sm font-medium transition disabled:opacity-50"
                >
                  <svg v-if="!saving" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <svg v-else class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  {{ saving ? t('common.saving') : t('common.saveChanges') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useForm } from '@inertiajs/vue3'
import { useNotifications, extractErrorMessage } from '@/composables/useNotifications'
import draggable from 'vuedraggable'

const { t } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({
  section: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['refresh'])

const slides = ref([])
const originalSlides = ref([])
const saving = ref(false)

// Check if there are unsaved changes
const hasChanges = computed(() => {
  return JSON.stringify(slides.value) !== JSON.stringify(originalSlides.value)
})

// Get image path with proper handling (defined early for use in watch)
const getImagePath = (image) => {
  if (!image || image === 'null' || image === 'undefined' || image === null) {
    return '/images/placeholder.svg' // return placeholder immediately
  }
  
  // Convert to string if it's not already
  const imagePath = String(image).trim()
  
  // If it already starts with http/https, return as is (full URL)
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }
  
  // If it already starts with /storage, return as is
  if (imagePath.startsWith('/storage/')) {
    return imagePath
  }
  
  // If it starts with storage/ (without leading slash), add leading slash
  if (imagePath.startsWith('storage/')) {
    return `/${imagePath}`
  }
  
  // Otherwise prepend /storage/
  return `/storage/${imagePath}`
}

// Initialize slides from section data
watch(() => props.section, (newSection) => {
  if (newSection && newSection.additional_data && newSection.additional_data.slides) {
    slides.value = JSON.parse(JSON.stringify(newSection.additional_data.slides))
    originalSlides.value = JSON.parse(JSON.stringify(newSection.additional_data.slides))
    
    // Debug: Log image paths
    console.log('🔍 Hero Slider - Slides loaded:', slides.value.length)
    slides.value.forEach((slide, index) => {
      const imagePath = getImagePath(slide.image)
      console.log(`  Slide ${index}:`, {
        title: slide.title_ar || slide.title_en,
        image: slide.image,
        imagePath: imagePath,
        hasImage: !!(slide.image && slide.image !== 'null' && slide.image !== null && slide.image.trim() !== ''),
        willDisplay: !!(slide.imagePreview || (slide.image && slide.image !== 'null' && slide.image !== null && slide.image.trim() !== ''))
      })
      
      // Test if image exists
      if (slide.image && slide.image !== 'null' && slide.image !== null) {
        const img = new Image()
        img.onload = () => console.log(`    ✅ Image ${index} exists and is accessible:`, imagePath)
        img.onerror = () => console.error(`    ❌ Image ${index} NOT FOUND or NOT ACCESSIBLE:`, imagePath)
        img.src = imagePath
      }
    })
  } else {
    slides.value = []
    originalSlides.value = []
  }
}, { immediate: true })

// Add new slide
const addSlide = () => {
  slides.value.push({
    id: Date.now(),
    title_ar: '',
    title_en: '',
    subtitle_ar: '',
    subtitle_en: '',
    description_ar: '',
    description_en: '',
    button_text_ar: '',
    button_text_en: '',
    button_url: '',
    image: null,
    imagePreview: null,
    imageFile: null,
    isEditing: true
  })
}

// Remove slide
const removeSlide = (index) => {
  if (confirm(t('landing_page.hero.confirmDelete'))) {
    slides.value.splice(index, 1)
  }
}

// Toggle slide edit mode
const toggleSlideEdit = (index) => {
  slides.value[index].isEditing = !slides.value[index].isEditing
}

// Compress and optimize image
const compressImage = async (file, maxWidth = 1920, maxHeight = 1080, quality = 0.80) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onerror = () => reject(new Error('Failed to read file'))
    reader.onload = (e) => {
      const img = new Image()
      img.onerror = () => reject(new Error('Failed to load image'))
      img.onload = () => {
        const canvas = document.createElement('canvas')
        let width = img.width
        let height = img.height

        console.log('📸 Original image size:', { 
          width: img.width, 
          height: img.height, 
          fileSize: `${(file.size / 1024).toFixed(2)} KB` 
        })

        // Calculate new dimensions maintaining aspect ratio
        if (width > maxWidth || height > maxHeight) {
          const ratio = Math.min(maxWidth / width, maxHeight / height)
          width = Math.round(width * ratio)
          height = Math.round(height * ratio)
          console.log('🔽 Resizing large image to:', { width, height })
        } else if (width < 800 || height < 450) {
          // Scale up small images
          const ratio = Math.min(1920 / width, 1080 / height)
          width = Math.round(width * ratio)
          height = Math.round(height * ratio)
          console.log('🔼 Scaling up small image to:', { width, height })
        }

        canvas.width = width
        canvas.height = height

        const ctx = canvas.getContext('2d')
        // Enable image smoothing for better quality
        ctx.imageSmoothingEnabled = true
        ctx.imageSmoothingQuality = 'high'
        
        // Fill with white background (for transparent images)
        ctx.fillStyle = '#FFFFFF'
        ctx.fillRect(0, 0, width, height)
        
        ctx.drawImage(img, 0, 0, width, height)

        // Adjust quality based on file size
        let compressionQuality = quality
        const originalSizeMB = file.size / (1024 * 1024)
        
        if (originalSizeMB > 5) {
          compressionQuality = 0.70 // More compression for large files
          console.log('🔽 Large file detected, using higher compression')
        } else if (originalSizeMB > 2) {
          compressionQuality = 0.75
        }

        canvas.toBlob((blob) => {
          if (!blob) {
            reject(new Error('Failed to compress image'))
            return
          }

          const optimizedFile = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), {
            type: 'image/jpeg',
            lastModified: Date.now()
          })
          
          const originalSize = (file.size / 1024).toFixed(2)
          const newSize = (optimizedFile.size / 1024).toFixed(2)
          const savedPercent = (((file.size - optimizedFile.size) / file.size) * 100).toFixed(1)
          
          console.log('✅ Image optimization complete:', {
            original: `${originalSize} KB`,
            compressed: `${newSize} KB`,
            saved: `${savedPercent}%`,
            dimensions: `${width}x${height}`
          })
          
          resolve(optimizedFile)
        }, 'image/jpeg', compressionQuality)
      }
      img.src = e.target.result
    }
    reader.readAsDataURL(file)
  })
}

// Handle slide image upload with compression
const handleSlideImageUpload = async (event, index) => {
  const file = event.target.files[0]
  if (!file) return

  console.log('📸 Processing image...', { name: file.name, size: `${(file.size / 1024).toFixed(2)} KB` })
  
  try {
    // Compress the image
    const compressedFile = await compressImage(file)
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      slides.value[index].imagePreview = e.target.result
      slides.value[index].imageFile = compressedFile
      console.log('✅ Image ready for upload')
    }
    reader.readAsDataURL(compressedFile)
  } catch (error) {
    console.error('❌ Image processing failed:', error)
    // Fallback to original file
    const reader = new FileReader()
    reader.onload = (e) => {
      slides.value[index].imagePreview = e.target.result
      slides.value[index].imageFile = file
    }
    reader.readAsDataURL(file)
  }
}

// Remove slide image
const removeSlideImage = (index) => {
  slides.value[index].image = null
  slides.value[index].imagePreview = null
  slides.value[index].imageFile = null
}

// Update slide order after drag
const updateSlideOrder = () => {
  // Order is automatically updated by draggable
}

// getImagePath is now defined earlier (before watch)

// Handle image load success
const handleImageLoad = (e) => {
  console.log('✅ Image loaded successfully:', e.target.src)
}

// Handle image error
const handleImageError = (e) => {
  console.error('❌ Image load error:', e.target.src)
  console.error('   Attempting to load:', e.target.src)
  
  // Check if it's a 404 or 403
  const img = new Image()
  img.onerror = () => {
    console.error('   Confirmed: Image does not exist or access denied')
  }
  img.src = e.target.src
  
  // Prevent infinite loop by checking if already showing placeholder
  if (!e.target.src.includes('placeholder') && !e.target.src.includes('data:')) {
    console.log('   Setting placeholder image')
    e.target.src = '/images/placeholder.svg'
  }
  // Stop further error events
  e.target.onerror = null
}

// Reset changes
const resetChanges = () => {
  if (confirm(t('landing_page.hero.confirmReset'))) {
    slides.value = JSON.parse(JSON.stringify(originalSlides.value))
  }
}

// Save slides
const saveSlides = () => {
  if (!props.section || !props.section.id) {
    error(t('landingSections.sectionNotFound'))
    return
  }

  // Prevent multiple simultaneous saves
  if (saving.value) {
    console.log('⚠️ Already saving, skipping...')
    return
  }

  console.log('💾 Saving slides...', slides.value.length)

  saving.value = true

  // Prepare form data object
  const formDataObject = {
    _method: 'PUT',
    section_key: props.section?.section_key || '',
    additional_data: {}
  }

  // Prepare slides data
  const slidesData = slides.value.map((slide, index) => {
    const slideData = {
      id: slide.id,
      title_ar: slide.title_ar,
      title_en: slide.title_en,
      subtitle_ar: slide.subtitle_ar,
      subtitle_en: slide.subtitle_en,
      description_ar: slide.description_ar,
      description_en: slide.description_en,
      button_text_ar: slide.button_text_ar,
      button_text_en: slide.button_text_en,
      button_url: slide.button_url,
      order: index
    }

    // Handle images - if there's a file, add it to form data
    if (slide.imageFile) {
      formDataObject[`slide_images[${index}]`] = slide.imageFile
      slideData.has_new_image = true
      console.log(`📸 Slide ${index} has new image file:`, slide.imageFile.name, `(${(slide.imageFile.size / 1024).toFixed(2)} KB)`)
    } else if (slide.image) {
      slideData.image = slide.image
    }

    return slideData
  })

  formDataObject.additional_data = JSON.stringify({ slides: slidesData })

  console.log('📤 Sending to server...')
  console.log('📦 Form data prepared:')
  console.log('  - Slides count:', slidesData.length)
  console.log('  - Images to upload:', slides.value.filter(s => s.imageFile).length)

  // Log each slide with image
  slides.value.forEach((slide, index) => {
    if (slide.imageFile) {
      console.log(`  - Slide ${index}: ${slide.imageFile.name} (${(slide.imageFile.size / 1024).toFixed(2)} KB)`)
    }
  })

  const form = useForm(formDataObject)

  // Use POST with _method: PUT for proper FormData handling with file uploads
  form.post(route('admin.landing-page-sections.update', props.section.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Slides saved successfully!')
      success(t('landing_page.hero.savedSuccessfully'))
      saving.value = false
      
      // Clear image previews and files after successful save
      slides.value.forEach(slide => {
        if (slide.imagePreview) {
          slide.imagePreview = null
          slide.imageFile = null
        }
      })
      
      emit('refresh')
    },
    onError: (errors) => {
      console.error('❌ Save failed:', errors)
      const message = extractErrorMessage(errors, t('landing_page.hero.saveFailed'))
      error(message)
      saving.value = false
    }
  })
}
</script>

<style scoped>
.drag-handle {
  cursor: move;
}
</style>


