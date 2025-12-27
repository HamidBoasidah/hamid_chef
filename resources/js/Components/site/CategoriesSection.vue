<template>
  <section class="relative py-20 lg:py-28 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <!-- Small Label -->
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="text-sm font-semibold text-primary/80 uppercase tracking-wider">
            {{ currentLang === 'ar' ? 'الفئات' : 'Categories' }}
          </span>
          <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
        <h2 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-primary dark:text-white mb-4">
          {{ currentLang === 'ar' ? section?.title_ar : section?.title_en }}
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
          {{ currentLang === 'ar' ? section?.description_ar : section?.description_en }}
        </p>
      </div>

      <!-- Note -->
      <div class="text-center text-gray-600 dark:text-gray-400">
        <p>{{ currentLang === 'ar' ? note?.ar : note?.en }}</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  section: {
    type: Object,
    required: true
  },
  currentLang: {
    type: String,
    default: 'ar'
  }
})

const note = computed(() => {
  const data = props.section?.additional_data
  if (!data) return null
  return {
    ar: data.note_ar,
    en: data.note_en
  }
})
</script>
