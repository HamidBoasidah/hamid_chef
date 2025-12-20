<template>
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
      <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t(label) }}</h2>
      <p v-if="maxImages" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ t('common.maxImages', { max: maxImages }) }} ({{ selectedFiles.length }}/{{ maxImages }})
      </p>
    </div>
    <div class="p-4 sm:p-6">
      <!-- Upload Area -->
      <label
        :for="inputId"
        class="shadow-theme-xs relative block cursor-pointer rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-800 dark:bg-gray-900"
        :class="{
          'border-brand-500 dark:border-brand-500': isDragOver,
          'opacity-50 cursor-not-allowed': isMaxReached
        }"
        @dragover.prevent="handleDragOver"
        @dragleave.prevent="handleDragLeave"
        @drop.prevent="handleDrop"
      >
        <div class="flex justify-center p-10">
          <div class="flex max-w-[260px] flex-col items-center gap-4">
            <div class="inline-flex h-13 w-13 items-center justify-center rounded-full border border-gray-200 text-gray-700 dark:border-gray-800 dark:text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M20.0004 16V18.5C20.0004 19.3284 19.3288 20 18.5004 20H5.49951C4.67108 20 3.99951 19.3284 3.99951 18.5V16M12.0015 4L12.0015 16M7.37454 8.6246L11.9994 4.00269L16.6245 8.6246" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
              <span class="font-medium text-gray-800 dark:text-white/90">
                {{ isMaxReached ? t('common.maxImagesReached') : t('common.clickToUpload') }}
              </span>
              <br v-if="!isMaxReached">
              <span v-if="!isMaxReached">{{ t('common.orDragDrop') }}</span>
            </p>
          </div>
        </div>
        <input 
          ref="inputRef" 
          :id="inputId" 
          type="file" 
          class="hidden" 
          :accept="accept" 
          :multiple="true"
          :disabled="isMaxReached"
          @change="onFileChange" 
        />
      </label>

      <!-- Preview Grid -->
      <div v-if="selectedFiles.length > 0" class="mt-6">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
          {{ t('common.selectedImages') }} ({{ selectedFiles.length }})
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="(file, index) in selectedFiles" 
            :key="index"
            class="relative rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-800 shadow-sm"
          >
            <div class="aspect-square relative">
              <img 
                :src="getPreviewUrl(file)" 
                :alt="`Preview ${index + 1}`" 
                class="w-full h-full object-cover"
              />

            </div>
            
            <!-- Remove button - positioned better and larger -->
            <button 
              type="button" 
              @click="removeFile(index)"
              class="absolute top-3 right-3 bg-error-500 text-white rounded-full p-2 shadow-lg z-10"
              :title="t('common.removeImage')"
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
            
            <!-- File name overlay -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-3">
              <p class="text-white text-sm font-medium truncate">
                {{ getFileName(file) }}
              </p>
            </div>
            
            <!-- Image index indicator -->
            <div class="absolute top-3 left-3 bg-black/60 text-white text-xs font-medium px-2 py-1 rounded-full">
              {{ index + 1 }}
            </div>
          </div>
        </div>
      </div>

      <!-- Upload Progress -->
      <div v-if="isUploading" class="mt-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ t('common.uploading') }}
          </span>
          <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ uploadProgress }}%
          </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
          <div 
            class="bg-brand-500 h-2 rounded-full" 
            :style="{ width: uploadProgress + '%' }"
          ></div>
        </div>
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
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  modelValue: { 
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
    default: 'multi-image-upload' 
  },
  label: { 
    type: String, 
    default: 'common.galleryImages' 
  }
})

const emit = defineEmits([
  'update:modelValue',
  'files-added',
  'file-removed',
  'validation-error'
])

const { t } = useI18n()

const inputRef = ref(null)
const selectedFiles = ref([])
const previewUrls = ref(new Map())
const validationErrors = ref([])
const isDragOver = ref(false)
const uploadProgress = ref(0)
const isUploading = ref(false)

const isMaxReached = computed(() => {
  return props.maxImages && selectedFiles.value.length >= props.maxImages
})

// Watch for external changes to modelValue
watch(
  () => props.modelValue,
  (newValue) => {
    if (Array.isArray(newValue)) {
      selectedFiles.value = [...newValue]
      updatePreviewUrls()
    }
  },
  { immediate: true }
)

function validateFile(file) {
  const errors = []
  
  // Check file type
  if (!props.acceptedTypes.includes(file.type)) {
    errors.push(t('validation.invalidFileType', { 
      name: file.name, 
      types: props.acceptedTypes.join(', ') 
    }))
  }
  
  // Check file size
  if (file.size > props.maxFileSize) {
    const maxSizeMB = (props.maxFileSize / (1024 * 1024)).toFixed(1)
    errors.push(t('validation.fileTooLarge', { 
      name: file.name, 
      maxSize: maxSizeMB 
    }))
  }
  
  return errors
}

function validateFiles(files) {
  const errors = []
  
  // Check max images limit
  if (props.maxImages && selectedFiles.value.length + files.length > props.maxImages) {
    errors.push(t('validation.tooManyFiles', { 
      max: props.maxImages,
      current: selectedFiles.value.length,
      adding: files.length
    }))
    return errors
  }
  
  // Validate each file
  files.forEach(file => {
    const fileErrors = validateFile(file)
    errors.push(...fileErrors)
  })
  
  return errors
}

function addFiles(files) {
  const fileArray = Array.from(files)
  const errors = validateFiles(fileArray)
  
  if (errors.length > 0) {
    validationErrors.value = errors
    emit('validation-error', errors)
    return
  }
  
  // Clear previous errors
  validationErrors.value = []
  
  // Add valid files
  const validFiles = fileArray.filter(file => validateFile(file).length === 0)
  selectedFiles.value.push(...validFiles)
  
  updatePreviewUrls()
  updateModelValue()
  
  emit('files-added', validFiles)
}

function removeFile(index) {
  const removedFile = selectedFiles.value[index]
  selectedFiles.value.splice(index, 1)
  
  // Clean up preview URL
  if (previewUrls.value.has(removedFile)) {
    URL.revokeObjectURL(previewUrls.value.get(removedFile))
    previewUrls.value.delete(removedFile)
  }
  
  updateModelValue()
  emit('file-removed', index)
  
  // Clear input value to allow re-selecting the same files
  if (inputRef.value) {
    inputRef.value.value = ''
  }
}

function updatePreviewUrls() {
  // Clean up old URLs
  previewUrls.value.forEach((url, file) => {
    if (!selectedFiles.value.includes(file)) {
      URL.revokeObjectURL(url)
      previewUrls.value.delete(file)
    }
  })
  
  // Create new URLs for new files
  selectedFiles.value.forEach(file => {
    if (file instanceof File && !previewUrls.value.has(file)) {
      previewUrls.value.set(file, URL.createObjectURL(file))
    }
  })
}

function updateModelValue() {
  emit('update:modelValue', [...selectedFiles.value])
}

function getPreviewUrl(file) {
  if (file instanceof File) {
    return previewUrls.value.get(file) || ''
  }
  // Handle existing images (strings)
  return file
}

function getFileName(file) {
  if (file instanceof File) {
    return file.name
  }
  // Handle existing images (strings) - extract filename from path
  return file.split('/').pop() || 'image'
}

function onFileChange(event) {
  const files = event.target.files
  if (files && files.length > 0) {
    addFiles(files)
  }
}

function handleDragOver(event) {
  if (!isMaxReached.value) {
    isDragOver.value = true
  }
}

function handleDragLeave(event) {
  isDragOver.value = false
}

function handleDrop(event) {
  isDragOver.value = false
  
  if (isMaxReached.value) {
    return
  }
  
  const files = event.dataTransfer.files
  if (files && files.length > 0) {
    addFiles(files)
  }
}

// Cleanup on unmount
onBeforeUnmount(() => {
  previewUrls.value.forEach(url => URL.revokeObjectURL(url))
  previewUrls.value.clear()
})
</script>

<style scoped>
/* Additional styles if needed */
</style>