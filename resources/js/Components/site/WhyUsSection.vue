<template>
  <section class="relative py-20 lg:py-28 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <!-- Small Label -->
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="text-sm font-semibold text-primary/80 uppercase tracking-wider">
            {{ currentLang === 'ar' ? 'الاختيار الأمثل لراحة طفلك' : 'The optimal choice for your child\'s comfort' }}
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

      <!-- Reasons Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
        <div 
          v-for="(reason, index) in reasons" 
          :key="index"
          class="text-center group"
        >
          <!-- Icon Container -->
          <div class="relative mb-6 flex justify-center">
            <div class="w-20 h-20 rounded-full bg-white dark:bg-gray-800 border-4 border-secondary flex items-center justify-center group-hover:border-primary transition-all duration-300 shadow-lg">
              <component :is="getIcon(reason.icon)" :size="32" class="text-primary" />
            </div>
          </div>

          <!-- Title -->
          <h3 class="text-lg font-bold text-primary dark:text-white mb-3">
            {{ currentLang === 'ar' ? reason.title_ar : reason.title_en }}
          </h3>

          <!-- Description -->
          <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ currentLang === 'ar' ? reason.description_ar : reason.description_en }}
          </p>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div 
          v-for="(stat, index) in stats" 
          :key="index"
          class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-200 dark:border-gray-700"
        >
          <div class="text-4xl font-bold text-primary mb-2">
            {{ stat.number }}
          </div>
          <div class="text-sm text-gray-600 dark:text-gray-400">
            {{ currentLang === 'ar' ? stat.label_ar : stat.label_en }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import * as LucideIcons from 'lucide-vue-next'

const { Heart } = LucideIcons

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

const reasons = computed(() => props.section?.additional_data?.reasons || [])
const stats = computed(() => props.section?.additional_data?.stats || [])

const getIcon = (iconName) => {
  return LucideIcons[iconName] || LucideIcons.Circle
}
</script>
