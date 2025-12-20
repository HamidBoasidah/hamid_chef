<template>
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
      <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t(label) }}</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ t('common.totalImages', { 
          current: totalImagesCount, 
          max: maxImages 
        }) }}
      </p>
    </div>
    <div class="p-4 sm:p-6">
      <!-- Existing Images -->
      <div v-if="displayExistingImages.length > 0" class="mb-6">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
          {{ t('common.existingImages') }} ({{ displayExistingImages.length }})
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="image in displayExistingImages" 
            :key="image.id"
            class="relative rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-800 shadow-sm"
            :class="{ 
              'opacity-60 ring-2 ring-error-200 dark:ring-error-800': image.markedForDeletion,
              'ring-1 ring-gray-200 dark:ring-gray-700': !image.markedForDeletion
            }"
          >
            <div class="aspect-square relative">
              <img 
                :src="getImageUrl(image.image)" 
                :alt="`Gallery image ${image.id}`" 
                class="w-full h-full object-cover"
                :class="{ 
                  'grayscale': image.markedForDeletion
                }"
              />
              <!-- Overlay for marked for deletion -->
              <div v-if="image.markedForDeletion" class="absolute inset-0 bg-error-500/20 flex items-center justify-center">
                <div class="bg-error-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                  {{ t('common.willBeDeleted') }}
                </div>
              </div>
            </div>
            
            <!-- Action button - positioned better and larger -->
            <button 
              type="button" 
              @click="toggleImageDeletion(image)"
              class="absolute top-3 right-3 rounded-full p-2 shadow-lg z-10"
              :class="image.markedForDeletion 
                ? 'bg-green-500 hover:bg-green-600 text-white' 
                : 'bg-error-500 hover:bg-error-600 text-white'"
              :title="image.markedForDeletion ? t('common.restoreImage') : t('common.markForDeletion')"
            >
              <svg v-if="image.markedForDeletion" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
            
            <!-- Status overlay -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-3">
              <p class="text-white text-sm font-medium">
                {{ image.markedForDeletion ? t('common.markedForDeletion') : t('common.existingImage') }}
              </p>
            </div>
            
            <!-- Image ID indicator -->
            <div class="absolute top-3 left-3 bg-black/60 text-white text-xs font-medium px-2 py-1 rounded-full">
              #{{ image.id }}
            </div>
          </div>
        </div>
      </div>

      <!-- New Images Upload -->
      <div v-if="!isMaxReached">
        <MultiImageUpload 
          v-model="newImages"
          :input-id="inputId"
          :label="t('common.addNewImages')"
          :max-images="remainingSlots"
          :accepted-types="acceptedTypes"
          :max-file-size="maxFileSize"
          @files-added="onFilesAdded"
          @file-removed="onFileRemoved"
          @validation-error="onValidationError"
        />
      </div>

      <!-- Max Images Reached Message -->
      <div v-else class="text-center py-8">
        <div class="inline-flex h-13 w-13 items-center justify-center rounded-full border border-gray-200 text-gray-700 dark:border-gray-800 dark:text-gray-400 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ t('common.maxImagesReached') }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
          {{ t('common.deleteExistingToAddNew') }}
        </p>
      </div>



      <!-- Validation Errors -->
      <div v-if="validationErrors.length > 0" class="mt-4">
        <div v-for="error in validationErrors" :key="error" class="text-sm text-error-500 mb-1">
          {{ error }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import MultiImageUpload from './MultiImageUpload.vue'

const props = defineProps({
  newImages: { 
    type: Array, 
    default: () => [] 
  },
  deleteIds: { 
    type: Array, 
    default: () => [] 
  },
  existingImages: { 
    type: Array, 
    default: () => [] 
  },
  maxImages: { 
    type: Number, 
    default: 10 
  },
  acceptedTypes: { 
    type: Array, 
    default: () => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'] 
  },
  maxFileSize: { 
    type: Number, 
    default: 5 * 1024 * 1024 // 5MB
  },
  inputId: { 
    type: String, 
    default: 'gallery-manager' 
  },
  label: { 
    type: String, 
    default: 'common.galleryImages' 
  }
})

const emit = defineEmits([
  'update:newImages',
  'update:deleteIds',
  'validation-error'
])

const { t } = useI18n()

const newImages = ref([...props.newImages])
const deleteIds = ref([...props.deleteIds])
const validationErrors = ref([])

// Create display version of existing images with deletion state
const displayExistingImages = computed(() => {
  return props.existingImages.map(image => ({
    ...image,
    markedForDeletion: deleteIds.value.includes(image.id)
  }))
})

const activeExistingCount = computed(() => {
  return props.existingImages.length - deleteIds.value.length
})

const totalImagesCount = computed(() => {
  return activeExistingCount.value + newImages.value.length
})

const remainingSlots = computed(() => {
  return Math.max(0, props.maxImages - totalImagesCount.value)
})

const isMaxReached = computed(() => {
  return remainingSlots.value <= 0
})



// Watch for external changes
watch(() => props.newImages, (val) => {
  newImages.value = [...val]
}, { deep: true })

watch(() => props.deleteIds, (val) => {
  deleteIds.value = [...val]
}, { deep: true })

// Emit changes
watch(newImages, (val) => {
  emit('update:newImages', [...val])
}, { deep: true })

watch(deleteIds, (val) => {
  emit('update:deleteIds', [...val])
}, { deep: true })

function toggleImageDeletion(image) {
  const index = deleteIds.value.indexOf(image.id)
  
  if (index > -1) {
    // Remove from deletion list (restore) - immediate UI feedback
    deleteIds.value.splice(index, 1)
  } else {
    // Add to deletion list - immediate UI feedback
    deleteIds.value.push(image.id)
  }
  
  // Force reactivity update for immediate visual feedback
  deleteIds.value = [...deleteIds.value]
}

function getImageUrl(imagePath) {
  if (imagePath.startsWith('http')) {
    return imagePath
  }
  return `/storage/${imagePath}`
}

function onFilesAdded(files) {
  // Clear previous errors
  validationErrors.value = []
}

function onFileRemoved(index) {
  // Clear previous errors
  validationErrors.value = []
}

function onValidationError(errors) {
  validationErrors.value = Array.isArray(errors) ? errors : [errors]
  emit('validation-error', errors)
}
</script>

<style scoped>
/* Additional styles if needed */
</style>