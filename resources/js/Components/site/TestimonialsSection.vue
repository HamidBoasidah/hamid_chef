<template>
  <section class="relative py-20 lg:py-28 bg-gray-50 dark:bg-gray-900">
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

      <!-- Testimonials Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="(testimonial, index) in testimonials" 
          :key="index"
          class="bg-white dark:bg-gray-800 rounded-lg p-8 border-2 border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300"
        >
          <!-- Rating -->
          <div class="flex gap-1 justify-center mb-6">
            <component 
              v-for="star in 5" 
              :key="star" 
              :is="Star" 
              :size="20" 
              :fill="star <= (testimonial.rating || 5) ? 'currentColor' : 'none'"
              class="text-primary" 
            />
          </div>

          <!-- Comment -->
          <p class="text-gray-700 dark:text-gray-300 text-base mb-6 leading-relaxed">
            {{ currentLang === 'ar' ? testimonial.comment_ar : testimonial.comment_en }}
          </p>

          <!-- Author -->
          <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <img 
              v-if="testimonial.avatar" 
              :src="testimonial.avatar" 
              :alt="currentLang === 'ar' ? testimonial.name_ar : testimonial.name_en" 
              class="w-12 h-12 rounded-full object-cover"
            />
            <div>
              <h4 class="font-semibold text-gray-900 dark:text-white">
                {{ currentLang === 'ar' ? testimonial.name_ar : testimonial.name_en }}
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { MessageCircle, Star } from 'lucide-vue-next'

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

const testimonials = computed(() => props.section?.additional_data?.testimonials || [])
</script>
