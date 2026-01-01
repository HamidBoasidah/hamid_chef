<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
          <div
            class="relative w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800 flex items-center justify-center group cursor-pointer"
            @click="triggerAvatarUpload"
          >
            <img :src="user?.avatar ? `/storage/${rawUser.avatar}` : '/images/user/owner.jpg'" alt="User" class="w-full h-full object-cover object-center" />
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <input
              ref="avatarInput"
              type="file"
              accept="image/*"
              class="hidden"
              @change="handleAvatarChange"
            />
          </div>
          <div class="order-3 xl:order-2">
            <h4
              class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left"
            >
              {{ user?.name || t('user.name', { default: 'Code Brains' }) }}
            </h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center xl:text-left">
              {{ user?.email || '-' }}
            </p>
          </div>
          <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">
            <button @click="isProfileInfoModal = true" class="edit-button">
          <svg
            class="fill-current"
            width="18"
            height="18"
            viewBox="0 0 18 18"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
              fill=""
            />
          </svg>
          {{ t('profile.edit') }}
        </button>
          </div>
        </div>
      </div>
    </div>
    <Modal v-if="isProfileInfoModal" @close="closeModal">
      <template #body>
        <div
          class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
        >
          <!-- close btn -->
          <button
            @click="closeModal"
            class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300"
          >
            <svg
              class="fill-current"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                fill=""
              />
            </svg>
          </button>
          <div class="px-2 pr-14">
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
              {{ t('profile.editPersonalInformation') }}
            </h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
              {{ t('profile.updateDetails') }}
            </p>
          </div>
          <form class="flex flex-col">
            <div class="custom-scrollbar overflow-y-auto p-2 max-h-[600px]">
              <!-- Profile Picture Section -->
              <div class="mb-7">
                <h5 class="mb-4 text-lg font-medium text-gray-800 dark:text-white/90">
                  {{ t('profile.profilePicture') }}
                </h5>
                <div class="flex items-center gap-4">
                  <div class="relative w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800 flex items-center justify-center avatar-preview">
                    <img 
                      ref="avatarPreview"
                      :src="avatarPreviewUrl || (user?.avatar && rawUser?.avatar ? `/storage/${rawUser.avatar}` : '/images/user/owner.jpg')" 
                      alt="User" 
                      class="w-full h-full object-cover object-center"
                      :key="rawUser?.avatar || avatarPreviewUrl || 'default'"
                    />
                  </div>
                  <div class="flex-1">
                    <button
                      type="button"
                      @click="triggerAvatarUpload"
                      class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      {{ t('profile.changePicture') }}
                    </button>
                    <input
                      ref="avatarInput"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleAvatarChange"
                    />
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                      {{ t('profile.pictureRequirements') }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Personal Information Section -->
              <div>
                <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90">
                  {{ t('profile.personalInformation') }}
                </h5>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                  <div class="col-span-2 lg:col-span-1">
                    <label
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                      {{ t('profile.labels.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                      type="text"
                      v-model="form.name"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                  </div>

                  <div class="col-span-2 lg:col-span-1">
                    <label
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                      {{ t('profile.labels.emailAddress') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                      type="email"
                      v-model="form.email"
                      class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                  </div>

                  <div class="col-span-2 lg:col-span-1">
                    <label
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                      {{ t('profile.labels.phone') }}
                    </label>
                    <input
                      type="text"
                      v-model="form.phone_number"
                      :placeholder="t('profile.labels.phonePlaceholder')"
                      class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                  </div>

                  <div class="col-span-2 lg:col-span-1">
                    <label
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                      {{ t('profile.labels.whatsapp') }}
                    </label>
                    <input
                      type="text"
                      v-model="form.whatsapp_number"
                      :placeholder="t('profile.labels.whatsappPlaceholder')"
                      class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                  </div>

                  <div class="col-span-2">
                    <label
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                      {{ t('profile.labels.address') }}
                    </label>
                    <input
                      type="text"
                      v-model="form.address"
                      :placeholder="t('profile.labels.addressPlaceholder')"
                      class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
          <button
            @click="closeModal"
            type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
          >
                {{ t('buttons.close') }}
              </button>
              <button
                @click="saveProfile"
                type="button"
                :disabled="form.processing"
                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto"
              >
                {{ form.processing ? t('buttons.saving', { default: 'جاري الحفظ...' }) : t('buttons.saveChanges') }}
              </button>
            </div>
          </form>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import Modal from './Modal.vue'
import { useI18n } from 'vue-i18n'
import { usePage, useForm, router } from '@inertiajs/vue3'
import { route } from '@/route'

const { t } = useI18n()
const page = usePage()
const rawUser = computed(() => page.props.value?.auth?.user || page.props.auth?.user || null)

const user = computed(() => {
  if (!rawUser.value) return null
  return {
    // prefer name attribute if model provides it, otherwise join first/last
    name: rawUser.value.name || [rawUser.value.first_name, rawUser.value.last_name].filter(Boolean).join(' ') || null,
    avatar: rawUser.value.avatar || null,
    bio: rawUser.value.bio || null,
    address: rawUser.value.address || null,
    email: rawUser.value.email || null,
    phone_number: rawUser.value.phone_number || null,
    social: {
      facebook: rawUser.value.facebook || null,
      x: rawUser.value.x_url || null,
      linkedin: rawUser.value.linkedin || null,
      instagram: rawUser.value.instagram || null,
    },
    first_name: rawUser.value.first_name || null,
    last_name: rawUser.value.last_name || null,
  }
})

const isProfileInfoModal = ref(false)
const avatarInput = ref(null)
const avatarPreview = ref(null)
const avatarPreviewUrl = ref(null)

const form = useForm({
  name: '',
  email: '',
  phone_number: '',
  whatsapp_number: '',
  address: '',
  avatar: null,
})

// Update form when user data changes
watch(rawUser, (newUser) => {
  if (newUser) {
    form.name = newUser.name || [newUser.first_name, newUser.last_name].filter(Boolean).join(' ') || ''
    form.email = newUser.email || ''
    form.phone_number = newUser.phone_number || ''
    form.whatsapp_number = newUser.whatsapp_number || ''
    form.address = newUser.address || ''
  }
}, { immediate: true })

const closeModal = () => {
  isProfileInfoModal.value = false
  // Reset avatar preview if not saved
  if (form.avatar && !form.processing) {
    form.avatar = null
    avatarPreviewUrl.value = null
    if (avatarInput.value) {
      avatarInput.value.value = ''
    }
  }
}

const triggerAvatarUpload = () => {
  avatarInput.value?.click()
}

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    alert(t('profile.errors.fileTooLarge', { default: 'File size must be less than 2MB' }))
    event.target.value = '' // Reset input
    return
  }

  // Validate file type
  if (!file.type.startsWith('image/')) {
    alert(t('profile.errors.invalidFileType', { default: 'Please select an image file' }))
    event.target.value = '' // Reset input
    return
  }

  // Update form with the file (don't save yet, wait for save button)
  form.avatar = file
  
  // Create preview URL for the selected image
  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreviewUrl.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const saveProfile = () => {
  // Split name into first_name and last_name
  const nameParts = form.name.trim().split(' ')
  const firstName = nameParts[0] || ''
  const lastName = nameParts.slice(1).join(' ') || ''
  
  const updateData = {
    first_name: firstName,
    last_name: lastName,
    email: form.email,
    phone_number: form.phone_number,
    whatsapp_number: form.whatsapp_number,
    address: form.address,
    avatar: form.avatar,
  }
  
  // Use patch method directly, and forceFormData if avatar exists
  const options = {
    preserveScroll: true,
    onSuccess: () => {
      isProfileInfoModal.value = false
      form.avatar = null
      avatarPreviewUrl.value = null
      if (avatarInput.value) {
        avatarInput.value.value = '' // Reset input
      }
      router.reload({ only: ['auth'] })
    },
    onError: (errors) => {
      console.error('Profile update errors:', errors)
    },
  }
  
  // If avatar exists, use forceFormData
  if (form.avatar) {
    options.forceFormData = true
  }
  
  // Use direct URL to avoid routing issues
  form.transform(() => updateData).patch('/profile', options)
}
</script>
