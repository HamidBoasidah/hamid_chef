<template>
  <!-- Services List Management -->
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ t('landingSections.servicesList') }}
          </h3>
          <button
            @click="showAddModal = true"
            class="btn-primary-outline  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ t('landingSections.addService') }}
          </button>
        </div>

        <!-- Services Table -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  {{ t('common.icon') }}
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  {{ t('common.titleAr') }}
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  {{ t('common.titleEn') }}
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  {{ t('common.actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="(service, index) in services" :key="index">
                <td class="px-4 py-4 whitespace-nowrap">
                  <div v-if="service.icon" class="w-12 h-12 flex items-center justify-center bg-secondary-50 dark:bg-primary-900/20 rounded-lg p-2">
                    <img :src="service.icon" alt="Icon" class="w-full h-full object-contain" />
                  </div>
                  <div v-else class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ service.title_ar }}</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ service.description_ar }}</div>
                </td>
                <td class="px-4 py-4">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ service.title_en }}</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ service.description_en }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="editService(index)"
                    class="btn-warning-outline mr-3"
                  >
                    {{ t('common.edit') }}
                  </button>
                  <button
                    @click="deleteService(index)"
                    class="btn-danger-outline"
                  >
                    {{ t('common.delete') }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add/Edit Service Modal -->
      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                {{ editingIndex !== null ? t('landingSections.editService') : t('landingSections.addService') }}
              </h3>

              <form @submit.prevent="saveService" class="space-y-4">
                <!-- Icon SVG Upload -->
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ t('landingSections.iconSvg') }}
                  </label>
                  
                  <!-- Icon Preview -->
                  <div v-if="serviceForm.icon || iconPreview" class="mb-3">
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                      <div class="w-16 h-16 flex items-center justify-center bg-white dark:bg-gray-800 rounded-xl border-2 border-secondary-200 dark:border-primary-700 shadow-md p-3">
                        <img :src="iconPreview || serviceForm.icon" alt="Icon" class="w-full h-full object-contain" />
                      </div>
                      <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ iconFileName || 'icon.svg' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">SVG Icon • Ready to use</div>
                      </div>
                      <button
                        type="button"
                        @click="removeIcon"
                        class="btn-danger-outline"
                        title="Remove icon"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Upload Button with Drag & Drop -->
                  <div
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleIconDrop"
                    :class="[
                      'relative border-2 border-dashed rounded-xl p-6 text-center transition-all duration-200',
                      isDragging 
                        ? 'border-secondary-500 dark:border-primary-500 bg-secondary-50 dark:bg-primary-900/20' 
                        : 'border-gray-300 dark:border-gray-700 hover:border-secondary-400 dark:hover:border-primary-600 bg-gray-50 dark:bg-gray-900'
                    ]"
                  >
                    <input
                      ref="iconInput"
                      type="file"
                      @change="handleIconUpload"
                      accept=".svg,image/svg+xml"
                      class="hidden"
                    />
                    
                    <div class="flex flex-col items-center justify-center gap-3">
                      <div class="w-12 h-12 bg-secondary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-secondary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                      </div>
                      
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                          {{ isDragging ? t('common.dropHere') : (serviceForm.icon || iconPreview ? t('common.changeIcon') : t('common.uploadIcon')) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                          {{ t('common.dragAndDrop') }} or <button type="button" @click="$refs.iconInput.click()" class="text-secondary-600 dark:text-primary-400 hover:underline font-medium">{{ t('common.browse') }}</button>
                        </p>
                      </div>
                      
                      <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        SVG files only • Max 2MB
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Title AR -->
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ t('common.titleAr') }}
                  </label>
                  <input
                    v-model="serviceForm.title_ar"
                    type="text"
                    required
                    class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  />
                </div>

                <!-- Title EN -->
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ t('common.titleEn') }}
                  </label>
                  <input
                    v-model="serviceForm.title_en"
                    type="text"
                    required
                    class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  />
                </div>

                <!-- Description AR -->
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ t('common.descriptionAr') }}
                  </label>
                  <textarea
                    v-model="serviceForm.description_ar"
                    rows="2"
                    class="w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none"
                  />
                </div>

                <!-- Description EN -->
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ t('common.descriptionEn') }}
                  </label>
                  <textarea
                    v-model="serviceForm.description_en"
                    rows="2"
                    class="w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-secondary-300 dark:focus:border-primary-300 focus:outline-hidden focus:ring-3 focus:ring-secondary-500/10 dark:focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none"
                  />
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4">
                  <button
                    type="submit"
                    class="btn-primary-outline  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition  inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                  >
                    {{ t('common.save') }}
                  </button>
                  <button
                    type="button"
                    @click="closeModal"
                    class="btn-warning-outline"
                  >
                    {{ t('common.cancel') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useForm } from '@inertiajs/vue3'
import { useNotifications, extractErrorMessage } from '@/composables/useNotifications'

const { t } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({
  section: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['refresh'])

const showAddModal = ref(false)
const editingIndex = ref(null)
const services = ref([])
const iconPreview = ref(null)
const iconFile = ref(null)
const iconFileName = ref('')
const isDragging = ref(false)
const iconInput = ref(null)

const serviceForm = ref({
  icon: '',
  title_ar: '',
  title_en: '',
  description_ar: '',
  description_en: ''
})

const showModal = computed(() => showAddModal.value || editingIndex.value !== null)

// Initialize services when section changes
watch(() => props.section, (newSection) => {
  if (newSection && newSection.additional_data) {
    services.value = newSection.additional_data.features || newSection.additional_data.items || []
  }
}, { immediate: true })

const editService = (index) => {
  editingIndex.value = index
  const service = services.value[index]
  serviceForm.value = {
    icon: service.icon || '',
    title_ar: service.title_ar || '',
    title_en: service.title_en || '',
    description_ar: service.description_ar || '',
    description_en: service.description_en || ''
  }
  
  // Set icon file name if icon exists
  if (service.icon) {
    const iconPath = service.icon.split('/')
    iconFileName.value = iconPath[iconPath.length - 1]
  }
}

const deleteService = async (index) => {
  if (confirm(t('common.confirmDelete'))) {
    services.value.splice(index, 1)
    await updateAdditionalData()
  }
}

// Handle icon upload
const handleIconUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  processIconFile(file)
}

// Handle icon drag and drop
const handleIconDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  if (!file) return

  processIconFile(file)
}

// Process icon file (shared logic for upload and drop)
const processIconFile = (file) => {
  // Check if it's SVG
  if (!file.type.includes('svg') && !file.name.endsWith('.svg')) {
    error(t('common.svgOnlyError') || 'Please upload SVG files only')
    return
  }

  // Check file size (2MB max)
  if (file.size > 2 * 1024 * 1024) {
    error(t('common.fileTooLarge') || 'File size must be less than 2MB')
    return
  }

  iconFile.value = file
  iconFileName.value = file.name

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    iconPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
  
  success(t('common.iconSelected') || 'Icon selected successfully')
}

// Remove icon
const removeIcon = () => {
  iconPreview.value = null
  iconFile.value = null
  iconFileName.value = ''
  serviceForm.value.icon = ''
}

const saveService = async () => {
  // Prepare service data
  const serviceData = { ...serviceForm.value }
  
  // Store the editing index before potentially changing it
  const currentEditingIndex = editingIndex.value
  
  if (currentEditingIndex !== null) {
    // Update existing service
    services.value[currentEditingIndex] = serviceData
  } else {
    // Add new service
    services.value.push(serviceData)
  }
  
  await updateAdditionalData(currentEditingIndex)
  closeModal()
}

const closeModal = () => {
  showAddModal.value = false
  editingIndex.value = null
  iconPreview.value = null
  iconFile.value = null
  iconFileName.value = ''
  serviceForm.value = {
    icon: '',
    title_ar: '',
    title_en: '',
    description_ar: '',
    description_en: ''
  }
}

const updateAdditionalData = (currentEditingIndex = null) => {
  if (!props.section) {
    error(t('landingSections.sectionNotFound'))
    return
  }

  // Prepare form data - CRITICAL: JSON stringify additional_data for FormData compatibility
  const formDataObject = {
    _method: 'PUT',
    section_key: props.section?.section_key || '',
    additional_data: JSON.stringify({
      ...props.section.additional_data,
      features: services.value
    })
  }

  // If there's an icon file to upload
  if (iconFile.value) {
    formDataObject.service_icon = iconFile.value
    
    // Mark which service has the new icon (the last one if adding, or the editing index)
    const targetIndex = currentEditingIndex !== null ? currentEditingIndex : services.value.length - 1
    formDataObject.service_icon_index = targetIndex
  }

  const form = useForm(formDataObject)

  // Use POST with _method: PUT for proper FormData handling
  form.post(route('admin.landing-page-sections.update', props.section.id), {
    onSuccess: () => {
      success(t('landingSections.servicesUpdated'))
      iconFile.value = null
      iconPreview.value = null
      iconFileName.value = ''
      emit('refresh')
    },
    onError: (errors) => {
      const message = extractErrorMessage(errors, t('landingSections.updateFailed'))
      error(message)
    },
    preserveScroll: true,
    forceFormData: true
  })
}
</script>
