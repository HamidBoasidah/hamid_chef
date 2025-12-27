<template>
  <section class="relative py-20 lg:py-28 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <!-- Image -->
        <div class="relative order-2 lg:order-1">
          <div v-if="section?.image" class="relative">
            <img 
              :src="`/storage/${section.image}`" 
              alt="About Us" 
              class="w-full h-auto rounded-lg shadow-2xl"
            />
          </div>
          <div v-else class="w-full h-96 bg-secondary/20 rounded-lg flex items-center justify-center">
            <svg class="w-64 h-64 text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>

        <!-- Content -->
        <div class="order-1 lg:order-2">
          <!-- Small Label -->
          <div class="inline-flex items-center gap-2 mb-4">
            <span class="text-sm font-semibold text-primary/80 uppercase tracking-wider">
              {{ currentLang === 'ar' ? 'تعرف علينا أكثر' : 'Learn More About Us' }}
            </span>
            <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>

          <!-- Title -->
          <h2 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-primary dark:text-white mb-4">
            {{ currentLang === 'ar' ? section?.title_ar : section?.title_en }}
          </h2>

          <!-- Description -->
          <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
            {{ currentLang === 'ar' ? section?.description_ar : section?.description_en }}
          </p>

          <!-- Story -->
          <div v-if="story" class="mb-8">
            <p class="text-base text-gray-700 dark:text-gray-400 leading-relaxed">
              {{ currentLang === 'ar' ? story.ar : story.en }}
            </p>
          </div>

          <!-- Values -->
          <div v-if="values?.length" class="grid grid-cols-2 gap-4">
            <div 
              v-for="(value, index) in values" 
              :key="index"
              class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
            >
              <h3 class="text-lg font-bold text-primary dark:text-white mb-2">
                {{ currentLang === 'ar' ? value.title_ar : value.title_en }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ currentLang === 'ar' ? value.description_ar : value.description_en }}
              </p>
            </div>
          </div>
        </div>
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

const story = computed(() => {
  const data = props.section?.additional_data
  if (!data) return null
  return {
    ar: data.story_ar,
    en: data.story_en
  }
})

const values = computed(() => props.section?.additional_data?.values || [])
</script>
