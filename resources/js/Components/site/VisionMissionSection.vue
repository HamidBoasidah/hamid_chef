<template>
  <section class="relative py-20 lg:py-28 bg-primary dark:bg-gray-900 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <!-- Small Label -->
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="text-sm font-semibold text-white/80 uppercase tracking-wider">
            {{ currentLang === 'ar' ? 'الاختيار الأمثل لراحة طفلك' : 'The optimal choice for your child\'s comfort' }}
          </span>
          <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
        <h2 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-white mb-4">
          {{ currentLang === 'ar' ? section?.title_ar : section?.title_en }}
        </h2>
        <p class="text-lg text-white/80 max-w-3xl mx-auto">
          {{ currentLang === 'ar' ? section?.description_ar : section?.description_en }}
        </p>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Vision -->
        <div v-if="vision" class="bg-white/10 backdrop-blur-sm rounded-lg p-8 border border-white/20">
          <div class="w-16 h-16 bg-secondary/20 rounded-lg flex items-center justify-center mb-6">
            <component :is="getIcon(vision.icon)" :size="32" class="text-secondary" />
          </div>
          <h3 class="text-2xl font-bold text-white mb-4">
            {{ currentLang === 'ar' ? vision.title_ar : vision.title_en }}
          </h3>
          <p class="text-white/80 leading-relaxed">
            {{ currentLang === 'ar' ? vision.description_ar : vision.description_en }}
          </p>
        </div>

        <!-- Mission -->
        <div v-if="mission" class="bg-white/10 backdrop-blur-sm rounded-lg p-8 border border-white/20">
          <div class="w-16 h-16 bg-secondary/20 rounded-lg flex items-center justify-center mb-6">
            <component :is="getIcon(mission.icon)" :size="32" class="text-secondary" />
          </div>
          <h3 class="text-2xl font-bold text-white mb-4">
            {{ currentLang === 'ar' ? mission.title_ar : mission.title_en }}
          </h3>
          <p class="text-white/80 leading-relaxed">
            {{ currentLang === 'ar' ? mission.description_ar : mission.description_en }}
          </p>
        </div>

        <!-- Values -->
        <div v-if="goals?.length" class="bg-white/10 backdrop-blur-sm rounded-lg p-8 border border-white/20">
          <div class="w-16 h-16 bg-secondary/20 rounded-lg flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white mb-4">
            {{ currentLang === 'ar' ? 'القيم' : 'Values' }}
          </h3>
          <div class="space-y-4">
            <div v-for="(goal, index) in goals.slice(0, 3)" :key="index">
              <h4 class="text-lg font-semibold text-white mb-2">
                {{ currentLang === 'ar' ? goal.title_ar : goal.title_en }}
              </h4>
              <p class="text-white/80 text-sm">
                {{ currentLang === 'ar' ? goal.description_ar : goal.description_en }}
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
import * as LucideIcons from 'lucide-vue-next'

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

const vision = computed(() => props.section?.additional_data?.vision)
const mission = computed(() => props.section?.additional_data?.mission)
const goals = computed(() => props.section?.additional_data?.goals || [])

const getIcon = (iconName) => {
  return LucideIcons[iconName] || LucideIcons.Circle
}
</script>
