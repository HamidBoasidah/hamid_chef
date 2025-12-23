<template>
  <form class="space-y-6" @submit.prevent="create">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">
          {{ t('categories.categoryInformation') }}
        </h2>
      </div>

      <div class="p-4 sm:p-6 dark:border-gray-800">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <div>
            <label for="category-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('categories.name') }}
            </label>
            <input
              v-model="form.name"
              type="text"
              id="category-name"
              autocomplete="off"
              :placeholder="t('categories.name')"
              class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
            <p v-if="form.errors.name" class="mt-1 text-sm text-error-500">{{ form.errors.name }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">
          {{ t('categories.iconManagement') }}
        </h2>
      </div>

      <div class="p-4 sm:p-6 dark:border-gray-800">
        <div class="space-y-4">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              {{ t('categories.icon') }}
            </label>
            <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
              {{ t('categories.iconRequirements') }}
            </p>
            
            <div class="flex items-center gap-4">
              <input
                ref="iconInput"
                type="file"
                accept=".svg"
                class="hidden"
                @change="handleIconUpload"
              />
              
              <button
                type="button"
                @click="triggerFileInput"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors duration-200"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                {{ t('categories.selectIcon') }}
              </button>
              
              <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 scale-95 translate-x-2"
                enter-to-class="opacity-100 scale-100 translate-x-0"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 scale-100 translate-x-0"
                leave-to-class="opacity-0 scale-95 translate-x-2"
              >
                <div v-if="iconFile" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                  <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span class="truncate max-w-48">{{ iconFile.name }}</span>
                  <button
                    type="button"
                    @click="removeIcon"
                    class="text-red-500 hover:text-red-700 transition-colors duration-200 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20"
                    :title="t('categories.removeIcon')"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </Transition>
            </div>
            
            <p v-if="form.errors.icon" class="mt-1 text-sm text-error-500">{{ form.errors.icon }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('common.status') }}</h2>
      </div>
      <div class="p-4 sm:p-6">
        <label
          for="toggle-active"
          class="flex cursor-pointer select-none items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-400"
        >
          <div class="relative">
            <input type="checkbox" id="toggle-active" class="sr-only" v-model="form.is_active" />
            <div
              class="block h-6 w-11 rounded-full"
              :class="form.is_active ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'"
            ></div>
            <div
              :class="form.is_active ? 'translate-x-full' : 'translate-x-0'"
              class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-theme-sm duration-300 ease-linear"
            ></div>
          </div>
          <span
            class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
            :class="{
              'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500': form.is_active,
              'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500': !form.is_active,
            }"
          >
            {{ form.is_active ? t('common.active') : t('common.inactive') }}
          </span>
        </label>
      </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
      <Link
        :href="route('admin.categories.index')"
        class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]"
      >
        {{ t('buttons.backToList') }}
      </Link>
      <button
        type="submit"
        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition"
        :class="{ 'cursor-not-allowed opacity-70': form.processing }"
        :disabled="form.processing"
      >
        {{ form.processing ? t('common.loading') : t('buttons.create') }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useNotifications } from '@/composables/useNotifications'
import { ref, nextTick } from 'vue'

const { t } = useI18n()
const { success, error } = useNotifications()

const iconFile = ref(null)
const iconInput = ref(null)

const form = useForm({
  name: '',
  is_active: true,
  icon: null,
})

// استخدام دالة منفصلة لتجنب الوميض
function triggerFileInput() {
  if (iconInput.value) {
    iconInput.value.click()
  }
}

async function handleIconUpload(event) {
  const file = event.target.files[0]
  if (!file) return
  
  // Validate file type
  if (!file.type.includes('svg')) {
    error(t('validation.invalidFileType', { name: file.name, types: 'SVG' }))
    // إعادة تعيين input بدون وميض
    event.target.value = ''
    return
  }
  
  // Validate file size (100KB = 100 * 1024 bytes)
  if (file.size > 100 * 1024) {
    error(t('validation.fileTooLarge', { name: file.name, maxSize: '0.1' }))
    // إعادة تعيين input بدون وميض
    event.target.value = ''
    return
  }
  
  // تحديث القيم بشكل متزامن لتجنب الوميض
  await nextTick()
  iconFile.value = file
  form.icon = file
}

function removeIcon() {
  iconFile.value = null
  form.icon = null
  // إعادة تعيين input بطريقة آمنة
  if (iconInput.value) {
    iconInput.value.value = ''
  }
}

function create() {
  form.post(route('admin.categories.store'), {
    onSuccess: () => {
      success(t('categories.categoryCreatedSuccessfully'))
      form.reset()
      iconFile.value = null
    },
    onError: () => {
      error(t('categories.categoryCreationFailed'))
    },
    preserveScroll: true,
  })
}
</script>
