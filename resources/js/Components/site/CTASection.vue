<template>
  <section class="relative py-20 lg:py-28 bg-primary">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center text-white space-y-8">
        <h2 class="text-3xl lg:text-4xl font-bold">
          {{ currentLang === 'ar' ? section?.title_ar : section?.title_en }}
        </h2>
        <p class="text-lg lg:text-xl max-w-3xl mx-auto">
          {{ currentLang === 'ar' ? section?.description_ar : section?.description_en }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
          <button class="px-8 py-4 bg-white text-primary font-bold rounded-lg hover:bg-secondary transition-colors">
            {{ currentLang === 'ar' ? 'ابدأ الآن' : 'Get Started' }}
          </button>
        </div>
      </div>
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
