<template>
  <section id="contact" class="relative py-20 lg:py-28 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <!-- Small Label -->
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="text-sm font-semibold text-primary/80 uppercase tracking-wider">
            {{ currentLang === 'ar' ? 'تواصل معنا' : 'Contact Us' }}
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

      <div class="grid lg:grid-cols-2 gap-12">
        <!-- Contact Info -->
        <div class="space-y-8">
          <!-- Email -->
          <div v-if="contactData?.email" class="flex items-start gap-4">
            <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center flex-shrink-0">
              <component :is="Mail" :size="24" class="text-primary" />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                {{ currentLang === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
              </h3>
              <a :href="`mailto:${contactData.email}`" class="text-primary hover:underline">
                {{ contactData.email }}
              </a>
            </div>
          </div>

          <!-- Phone -->
          <div v-if="contactData?.phone" class="flex items-start gap-4">
            <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center flex-shrink-0">
              <component :is="Phone" :size="24" class="text-primary" />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                {{ currentLang === 'ar' ? 'الهاتف' : 'Phone' }}
              </h3>
              <a :href="`tel:${contactData.phone}`" class="text-primary hover:underline">
                {{ contactData.phone }}
              </a>
            </div>
          </div>

          <!-- Address -->
          <div v-if="contactData?.address_ar || contactData?.address_en" class="flex items-start gap-4">
            <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center flex-shrink-0">
              <component :is="MapPin" :size="24" class="text-primary" />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                {{ currentLang === 'ar' ? 'العنوان' : 'Address' }}
              </h3>
              <p class="text-gray-600 dark:text-gray-300">
                {{ currentLang === 'ar' ? contactData.address_ar : contactData.address_en }}
              </p>
            </div>
          </div>

          <!-- Working Hours -->
          <div v-if="contactData?.working_hours_ar || contactData?.working_hours_en" class="flex items-start gap-4">
            <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center flex-shrink-0">
              <component :is="Clock" :size="24" class="text-primary" />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                {{ currentLang === 'ar' ? 'ساعات العمل' : 'Working Hours' }}
              </h3>
              <p class="text-gray-600 dark:text-gray-300">
                {{ currentLang === 'ar' ? contactData.working_hours_ar : contactData.working_hours_en }}
              </p>
            </div>
          </div>

          <!-- Social Links -->
          <div v-if="socialLinks?.length" class="flex gap-4 pt-4">
            <a 
              v-for="(link, index) in socialLinks" 
              :key="index"
              :href="link.url"
              target="_blank"
              class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-white hover:bg-primary-600 transition-colors"
            >
              <component :is="getSocialIcon(link.platform)" :size="20" />
            </a>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 border-2 border-gray-200 dark:border-gray-700">
          <form @submit.prevent="submitForm" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ currentLang === 'ar' ? 'الاسم' : 'Name' }}
              </label>
              <input 
                v-model="form.name"
                type="text" 
                required
                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ currentLang === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
              </label>
              <input 
                v-model="form.email"
                type="email" 
                required
                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ currentLang === 'ar' ? 'الرسالة' : 'Message' }}
              </label>
              <textarea 
                v-model="form.message"
                rows="4" 
                required
                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary focus:outline-none transition-colors resize-none"
              ></textarea>
            </div>

            <button 
              type="submit"
              class="w-full px-6 py-4 bg-primary text-white font-bold rounded-lg hover:bg-primary-600 transition-colors"
            >
              {{ currentLang === 'ar' ? 'إرسال' : 'Send Message' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Mail, Phone, MapPin, Clock, Facebook, Twitter, Instagram, Linkedin } from 'lucide-vue-next'

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

const contactData = computed(() => props.section?.additional_data || {})
const socialLinks = computed(() => props.section?.additional_data?.social_links || [])

const form = ref({
  name: '',
  email: '',
  message: ''
})

const submitForm = () => {
  // Handle form submission
  console.log('Form submitted:', form.value)
}

const getSocialIcon = (platform) => {
  const icons = {
    facebook: Facebook,
    twitter: Twitter,
    instagram: Instagram,
    linkedin: Linkedin
  }
  return icons[platform] || Mail
}
</script>
