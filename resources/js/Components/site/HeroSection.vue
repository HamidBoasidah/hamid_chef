<template>
  <section class="relative min-h-screen flex items-center justify-center bg-white dark:bg-gray-900 overflow-hidden">
    <!-- Decorative Shapes -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-20 left-10 w-64 h-64 bg-secondary/30 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 right-10 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <!-- Text Content -->
        <div class="text-center lg:text-start space-y-8">
          <!-- Small Label -->
          <div class="inline-flex items-center gap-2 mb-4">
            <span class="text-sm font-semibold text-primary/80 uppercase tracking-wider">
              {{ currentLang === 'ar' ? 'الاختيار الأمثل لراحة طفلك' : 'The optimal choice for your child\'s comfort' }}
            </span>
            <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>

          <!-- Main Title -->
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight text-primary dark:text-white">
            {{ currentLang === 'ar' ? section?.title_ar : section?.title_en }}
          </h1>

          <!-- Subtitle -->
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-gray-700 dark:text-gray-300">
            {{ currentLang === 'ar' ? (section?.subtitle_ar || '') : (section?.subtitle_en || 'We provide the best childcare services') }}
          </h2>

          <!-- Description -->
          <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
            {{ currentLang === 'ar' ? section?.description_ar : section?.description_en }}
          </p>

          <!-- CTA Button -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-4">
            <a href="#services" class="px-8 py-4 bg-primary text-white font-bold rounded-lg hover:bg-primary-600 transition-colors duration-300 shadow-lg">
              {{ currentLang === 'ar' ? 'ابدأ الآن' : 'Get Started' }}
            </a>
          </div>
        </div>

        <!-- Image/Illustration -->
        <div class="relative flex items-center justify-center">
          <div v-if="section?.image" class="relative">
            <img 
              :src="`/storage/${section.image}`" 
              alt="Hero Image" 
              class="w-full h-auto max-w-lg mx-auto rounded-lg shadow-2xl"
            />
          </div>
          <div v-else class="w-full h-96 bg-secondary/20 rounded-lg flex items-center justify-center">
            <svg class="w-64 h-64 text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
      <svg class="w-6 h-6 text-primary dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
      </svg>
    </div>
  </section>
</template>

<script setup>
defineProps({
  section: {
    type: Object,
    required: true
  },
  currentLang: {
    type: String,
    default: 'ar'
  }
})
</script>
