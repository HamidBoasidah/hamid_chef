<template>
  <section class="relative py-20 lg:py-28 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <!-- Small Label -->
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="text-sm font-semibold text-primary/80 uppercase tracking-wider">
            {{ currentLang === 'ar' ? 'كيف يعمل' : 'How It Works' }}
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

      <!-- Steps -->
      <div class="grid md:grid-cols-3 gap-8">
        <div 
          v-for="(step, index) in steps" 
          :key="index"
          class="relative text-center"
        >
          <!-- Step Number -->
          <div class="relative mb-6 flex justify-center">
            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
              {{ step.step || index + 1 }}
            </div>
            <!-- Connector Line (except last) -->
            <div 
              v-if="index < steps.length - 1"
              class="hidden md:block absolute top-1/2 left-full w-full h-0.5 bg-secondary -translate-y-1/2 z-0"
            ></div>
          </div>

          <!-- Image if available -->
          <div v-if="step.image" class="mb-6">
            <img 
              :src="`/storage/${step.image}`" 
              :alt="currentLang === 'ar' ? step.title_ar : step.title_en"
              class="w-full h-48 object-cover rounded-lg"
            />
          </div>

          <!-- Icon -->
          <div v-else class="w-20 h-20 mx-auto mb-6 bg-secondary/50 rounded-full flex items-center justify-center">
            <component :is="getIcon(step.icon)" :size="40" class="text-primary" />
          </div>

          <!-- Title -->
          <h3 class="text-xl font-bold text-primary dark:text-white mb-3">
            {{ currentLang === 'ar' ? step.title_ar : step.title_en }}
          </h3>

          <!-- Description -->
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ currentLang === 'ar' ? step.description_ar : step.description_en }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import * as LucideIcons from 'lucide-vue-next'

const { Zap } = LucideIcons

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

const steps = computed(() => props.section?.additional_data?.steps || [])

const getIcon = (iconName) => {
  return LucideIcons[iconName] || LucideIcons.Circle
}
</script>
