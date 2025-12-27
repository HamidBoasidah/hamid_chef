<template>
  <section class="relative py-20 lg:py-28 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <!-- Small Label -->
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="text-sm font-semibold text-primary/80 uppercase tracking-wider">
            {{ currentLang === 'ar' ? 'ما نقدمه' : 'What We Offer' }}
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

      <!-- Features Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div 
          v-for="(feature, index) in features" 
          :key="index"
          class="text-center group"
        >
          <!-- Icon Container -->
          <div class="relative mb-6 flex justify-center">
            <div class="w-24 h-24 rounded-full bg-secondary/50 border-4 border-secondary flex items-center justify-center group-hover:bg-secondary group-hover:border-primary transition-all duration-300">
              <img v-if="feature.icon" :src="`/storage/${feature.icon}`" alt="Icon" class="w-12 h-12 object-contain" />
            </div>
          </div>

          <!-- Title -->
          <h3 class="text-lg font-bold text-primary dark:text-white mb-3">
            {{ currentLang === 'ar' ? feature.title_ar : feature.title_en }}
          </h3>

          <!-- Description -->
          <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ currentLang === 'ar' ? feature.description_ar : feature.description_en }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { Sparkles } from 'lucide-vue-next'

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

const features = computed(() => props.section?.additional_data?.features || [])
</script>
