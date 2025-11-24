<template>
  <button
    class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-700 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
    @click.prevent="onToggle"
    :title="title"
    aria-label="Toggle direction"
  >
    <!-- Globe icon -->
    <svg
      width="20"
      height="20"
      viewBox="0 0 24 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      class="text-gray-600 dark:text-gray-300"
    >
      <path
        fill="currentColor"
        d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2Zm7.938 9h-3.12a14.28 14.28 0 0 0-1.083-4.43A8.01 8.01 0 0 1 19.938 11ZM16.5 11h-4V6.126c1.28.21 2.467 1.83 3.154 4.874ZM11.5 6.126V11h-4c.687-3.044 1.875-4.664 3.154-4.874ZM7.265 6.57A14.28 14.28 0 0 0 6.182 11H3.062a8.01 8.01 0 0 1 4.203-4.43ZM3.062 13h3.12c.17 1.58.58 3.06 1.083 4.43A8.01 8.01 0 0 1 3.062 13ZM7.5 13h4v4.874c-1.28-.21-2.467-1.83-3.154-4.874Zm5 4.874V13h4c-.687 3.044-1.875 4.664-3.154 4.874ZM16.735 17.43c.503-1.37.913-2.85 1.083-4.43h3.12a8.01 8.01 0 0 1-4.203 4.43Z"
      />
    </svg>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { i18n } from '@/i18n'
import { switchLocale } from '@/composables/useLocale' // 👈 المهم

const title = computed(() =>
  i18n.global.locale.value === 'ar'
    ? i18n.global.t('common.switchToLTR')
    : i18n.global.t('common.switchToRTL')
)

async function onToggle() {
  const next = i18n.global.locale.value === 'ar' ? 'en' : 'ar'
  await switchLocale(next) // 👈 يحدّث i18n + <html lang/dir> + يحفظ في السيرفر
}
</script>
