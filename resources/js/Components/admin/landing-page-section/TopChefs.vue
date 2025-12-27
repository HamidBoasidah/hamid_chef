<template>
  <div class="p-6">
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-8 border border-blue-200 dark:border-gray-700">
      <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
          <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="flex-1">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            {{ t('landing_page.top_chefs.title') }}
          </h3>
          <p class="text-gray-700 dark:text-white mb-4">
            {{ t('landing_page.top_chefs.description') }}
          </p>
          
          <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">
              {{ t('landing_page.top_chefs.automatic_display') }}
            </h4>
            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ t('landing_page.top_chefs.feature1') }}
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ t('landing_page.top_chefs.feature2') }}
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ t('landing_page.top_chefs.feature3') }}
              </li>
            </ul>
          </div>

          <div v-if="section?.additional_data?.note_ar" class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-sm text-yellow-800 dark:text-yellow-200">
              <strong>{{ t('landing_page.common.note') }}:</strong> {{ section.additional_data.note_ar }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
  section: {
    type: Object,
    required: true
  }
});
</script>
