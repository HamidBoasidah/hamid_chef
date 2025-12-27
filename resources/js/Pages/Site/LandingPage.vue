<template>
  <div class="min-h-screen bg-white dark:bg-gray-900" :dir="currentLocale === 'ar' ? 'rtl' : 'ltr'">
    <!-- Navbar -->
    <SiteNavbar 
      transparent 
      :current-lang="currentLocale"
      :nav-items="navItems"
      :is-dark-mode="isDarkMode"
      @toggle-language="toggleLanguage"
      @toggle-dark-mode="toggleDarkMode"
    />

    <!-- Hero Section -->
    <HeroSection 
      v-if="sections.hero"
      id="hero"
      :section="sections.hero" 
      :current-lang="currentLocale" 
    />

    <!-- Features Section -->
    <FeaturesSection 
      v-if="sections.features"
      id="features"
      :section="sections.features" 
      :current-lang="currentLocale" 
    />

    <!-- How It Works Section -->
    <HowItWorksSection 
      v-if="sections.how_it_works"
      id="how-it-works"
      :section="sections.how_it_works" 
      :current-lang="currentLocale" 
    />

    <!-- Top Chefs Section -->
    <TopChefsSection 
      v-if="sections.top_chefs"
      id="top-chefs"
      :section="sections.top_chefs" 
      :current-lang="currentLocale" 
    />

    <!-- Categories Section -->
    <CategoriesSection 
      v-if="sections.categories"
      id="categories"
      :section="sections.categories" 
      :current-lang="currentLocale" 
    />

    <!-- Testimonials Section -->
    <TestimonialsSection 
      v-if="sections.testimonials"
      id="testimonials"
      :section="sections.testimonials" 
      :current-lang="currentLocale" 
    />

    <!-- About Us Section -->
    <AboutUsSection 
      v-if="sections.about_us"
      id="about"
      :section="sections.about_us" 
      :current-lang="currentLocale" 
    />

    <!-- Vision & Mission Section -->
    <VisionMissionSection 
      v-if="sections.vision_mission"
      id="vision-mission"
      :section="sections.vision_mission" 
      :current-lang="currentLocale" 
    />

    <!-- Why Us Section -->
    <WhyUsSection 
      v-if="sections.why_us"
      id="why-us"
      :section="sections.why_us" 
      :current-lang="currentLocale" 
    />

    <!-- Contact Section -->
    <ContactSection 
      v-if="sections.contact"
      id="contact"
      :section="sections.contact" 
      :current-lang="currentLocale" 
    />

    <!-- Footer -->
    <SiteFooter :current-lang="currentLocale" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  HeroSection, 
  WhyUsSection, 
  HowItWorksSection,
  FeaturesSection,
  TestimonialsSection,
  ContactSection,
  AboutUsSection,
  VisionMissionSection,
  TopChefsSection,
  CategoriesSection,
  SiteNavbar,
  SiteFooter
} from '@/Components/site'

const props = defineProps({
  sections: {
    type: Object,
    required: true
  },
  locale: {
    type: String,
    default: 'ar'
  }
})

const currentLocale = ref(props.locale)
const isDarkMode = ref(false)

// Navigation items
const navItems = ref([
  {
    href: '#hero',
    label_ar: 'الرئيسية',
    label_en: 'Home'
  },
  {
    href: '#features',
    label_ar: 'المميزات',
    label_en: 'Features'
  },
  {
    href: '#how-it-works',
    label_ar: 'كيف يعمل',
    label_en: 'How It Works'
  },
  {
    href: '#why-us',
    label_ar: 'لماذا نحن',
    label_en: 'Why Us'
  },
  {
    href: '#about',
    label_ar: 'من نحن',
    label_en: 'About'
  },
  {
    href: '#contact',
    label_ar: 'تواصل معنا',
    label_en: 'Contact'
  }
])

// Initialize dark mode from localStorage
onMounted(() => {
  const savedDarkMode = localStorage.getItem('darkMode')
  if (savedDarkMode === 'true') {
    isDarkMode.value = true
    document.documentElement.classList.add('dark')
  } else if (savedDarkMode === 'false') {
    isDarkMode.value = false
    document.documentElement.classList.remove('dark')
  } else {
    // Check system preference
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    isDarkMode.value = prefersDark
    if (prefersDark) {
      document.documentElement.classList.add('dark')
    }
  }
})

const toggleLanguage = () => {
  const newLocale = currentLocale.value === 'ar' ? 'en' : 'ar'
  
  router.post(route('locale.switch'), {
    locale: newLocale
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      currentLocale.value = newLocale
      window.location.reload()
    }
  })
}

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  
  if (isDarkMode.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('darkMode', 'true')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('darkMode', 'false')
  }
}
</script>

<style scoped>
/* Animation Keyframes */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

@keyframes float-delayed {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-30px);
  }
}

@keyframes float-slow {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  50% {
    transform: translateY(-15px) translateX(15px);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
  animation: float-delayed 8s ease-in-out infinite;
}

.animate-float-slow {
  animation: float-slow 10s ease-in-out infinite;
}
</style>
