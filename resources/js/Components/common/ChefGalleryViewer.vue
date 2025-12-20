<template>
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
      <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t(label) }}</h2>
      <p v-if="images.length > 0" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ t('common.imagesCount', { count: images.length }) }}
      </p>
    </div>
    
    <div class="p-4 sm:p-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-500"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="images.length === 0" class="text-center py-12">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full border border-gray-200 text-gray-400 dark:border-gray-800 dark:text-gray-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ emptyMessage || t('common.noGalleryImages') }}
        </p>
      </div>

      <!-- Gallery Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div 
          v-for="(image, index) in images" 
          :key="image.id || index"
          class="relative rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-800 cursor-pointer shadow-sm border border-gray-200 dark:border-gray-700"
          @click="openLightbox(index)"
        >
          <div class="aspect-square relative">
            <img 
              :src="getImageUrl(image.image)" 
              :alt="`Gallery image ${index + 1}`" 
              class="w-full h-full object-cover"
              loading="lazy"
              @load="onImageLoad"
              @error="onImageError"
            />

          </div>
          

          
          <!-- Image number indicator -->
          <div class="absolute top-3 left-3 bg-black/60 text-white text-sm font-medium px-2 py-1 rounded-full">
            {{ index + 1 }} / {{ images.length }}
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <Teleport to="body">
      <div 
        v-if="lightboxOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90"
        @click="closeLightbox"
      >
          <button 
            @click.stop="closeLightbox"
            class="absolute top-4 right-4 text-white z-10"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <button 
            v-if="currentIndex > 0"
            @click.stop="previousImage"
            class="absolute left-4 text-white z-10"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <div class="max-w-7xl max-h-screen p-4" @click.stop>
            <img 
              :src="getImageUrl(images[currentIndex].image)" 
              :alt="`Gallery image ${currentIndex + 1}`"
              class="max-w-full max-h-[90vh] object-contain mx-auto"
            />
            <div class="text-center text-white mt-4">
              {{ currentIndex + 1 }} / {{ images.length }}
            </div>
          </div>

          <button 
            v-if="currentIndex < images.length - 1"
            @click.stop="nextImage"
            class="absolute right-4 text-white z-10"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  images: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: 'common.gallery'
  },
  emptyMessage: {
    type: String,
    default: null
  }
})

const { t } = useI18n()

const lightboxOpen = ref(false)
const currentIndex = ref(0)

function getImageUrl(imagePath) {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) {
    return imagePath
  }
  return `/storage/${imagePath}`
}

function openLightbox(index) {
  currentIndex.value = index
  lightboxOpen.value = true
  document.body.style.overflow = 'hidden'
}

function closeLightbox() {
  lightboxOpen.value = false
  document.body.style.overflow = ''
}

function nextImage() {
  if (currentIndex.value < props.images.length - 1) {
    currentIndex.value++
  }
}

function previousImage() {
  if (currentIndex.value > 0) {
    currentIndex.value--
  }
}

// Keyboard navigation
function handleKeydown(event) {
  if (!lightboxOpen.value) return
  
  if (event.key === 'Escape') {
    closeLightbox()
  } else if (event.key === 'ArrowRight') {
    nextImage()
  } else if (event.key === 'ArrowLeft') {
    previousImage()
  }
}

function onImageLoad(event) {
  event.target.style.opacity = '1'
}

function onImageError(event) {
  event.target.style.opacity = '0.5'
  console.warn('Failed to load image:', event.target.src)
}

// Add keyboard event listener
if (typeof window !== 'undefined') {
  window.addEventListener('keydown', handleKeydown)
}
</script>

