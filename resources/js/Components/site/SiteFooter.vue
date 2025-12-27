<template>
  <footer class="relative bg-primary dark:bg-gray-950 text-white border-t border-white/10">
    
    <!-- Main Footer Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 relative z-10">
        
        <!-- Company Info -->
        <div class="lg:col-span-1">
          <slot name="brand">
            <div class="mb-6">
              <!-- Logo -->
              <div v-if="logoUrl" class="mb-4">
                <img 
                  :src="logoUrl" 
                  :alt="logoAlt" 
                  class="h-16 w-auto object-contain max-w-[200px]"
                  @error="handleLogoError"
                />
              </div>
              <!-- Company Name (always show if no logo or logo fails) -->
              <h3 v-if="companyName" class="text-2xl font-bold mb-2">
                {{ currentLang === 'ar' ? companyNameAr : companyName }}
              </h3>
            </div>
            <p v-if="description" class="text-sm opacity-80 mb-6 leading-relaxed">
              {{ currentLang === 'ar' ? descriptionAr : description }}
            </p>
          </slot>
          
          <!-- Social Links -->
          <div v-if="socialLinks.length" class="flex items-center gap-3">
            <a
              v-for="social in socialLinks"
              :key="social.name"
              :href="social.url"
              target="_blank"
              rel="noopener noreferrer"
              class="glass-social-link"
              :aria-label="social.name"
            >
              <component :is="getSocialIcon(social.name)" class="w-5 h-5" />
            </a>
          </div>
        </div>
        
        <!-- Link Columns -->
        <template v-for="(column, index) in linkColumns" :key="index">
          <div class="glass-footer-column">
            <h3 class="glass-footer-heading">
              {{ currentLang === 'ar' ? column.title_ar : column.title }}
            </h3>
            <ul class="space-y-3">
              <li v-for="link in column.links" :key="link.href">
                <Link v-if="!link.href.startsWith('#') && !link.href.startsWith('http')" :href="link.href" class="glass-footer-link">
                  {{ currentLang === 'ar' ? link.label_ar : link.label }}
                </Link>
                <a v-else :href="link.href" class="glass-footer-link">
                  {{ currentLang === 'ar' ? link.label_ar : link.label }}
                </a>
              </li>
            </ul>
          </div>
        </template>
        
        <!-- Contact Info / Newsletter -->
        <div class="lg:col-span-1">
          <slot name="contact">
            <h3 class="glass-footer-heading">
              {{ currentLang === 'ar' ? 'تواصل معنا' : 'Contact Us' }}
            </h3>
            
            <div v-if="contactInfo" class="space-y-4 mb-6">
              <div v-if="contactInfo.phone" class="flex items-center gap-3">
                <div class="glass-contact-icon">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <a :href="'tel:' + contactInfo.phone" class="text-sm hover:opacity-80 transition-opacity" dir="ltr">
                  {{ contactInfo.phone }}
                </a>
              </div>
              
              <div v-if="contactInfo.email" class="flex items-center gap-3">
                <div class="glass-contact-icon">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <a :href="'mailto:' + contactInfo.email" class="text-sm hover:opacity-80 transition-opacity">
                  {{ contactInfo.email }}
                </a>
              </div>
              
              <div v-if="contactInfo.address" class="flex items-start gap-3">
                <div class="glass-contact-icon mt-0.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <span class="text-sm opacity-80">
                  {{ currentLang === 'ar' ? contactInfo.address_ar : contactInfo.address }}
                </span>
              </div>
            </div>
          </slot>
          
        </div>
      </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="border-t border-white/10 bg-primary-800 dark:bg-black/20 relative z-10">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col items-center justify-center gap-4">
          <p class="text-sm opacity-70 text-center">
            <template v-if="currentLang === 'ar'">
              © {{ new Date().getFullYear() }} جميع الحقوق محفوظة لشركة {{ companyNameAr || companyName }} | تصميم وتطوير 
              <a href="https://codebrains.net" target="_blank" rel="noopener noreferrer" class="hover:opacity-100 transition-opacity underline">CodeBrains</a>
            </template>
            <template v-else>
              © {{ new Date().getFullYear() }} All rights reserved to {{ companyName }} | Designed & Developed by 
              <a href="https://codebrains.net" target="_blank" rel="noopener noreferrer" class="hover:opacity-100 transition-opacity underline">CodeBrains</a>
            </template>
          </p>
          
          <div v-if="bottomLinks.length" class="flex items-center gap-6">
            <a
              v-for="link in bottomLinks"
              :key="link.href"
              :href="link.href"
              class="text-sm opacity-70 hover:opacity-100 transition-opacity"
            >
              {{ currentLang === 'ar' ? link.label_ar : link.label }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed, h } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  /**
   * Use dark footer style
   */
  dark: {
    type: Boolean,
    default: true
  },
  /**
   * Current language
   */
  currentLang: {
    type: String,
    default: 'en'
  },
  /**
   * Company logo URL
   */
  logo: {
    type: String,
    default: null
  },
  /**
   * Logo alt text
   */
  logoAlt: {
    type: String,
    default: 'Logo'
  },
  /**
   * Company name (English)
   */
  companyName: {
    type: String,
    default: null
  },
  /**
   * Company name (Arabic)
   */
  companyNameAr: {
    type: String,
    default: null
  },
  /**
   * Company description (English)
   */
  description: {
    type: String,
    default: null
  },
  /**
   * Company description (Arabic)
   */
  descriptionAr: {
    type: String,
    default: null
  },
  /**
   * Social media links
   */
  socialLinks: {
    type: Array,
    default: () => []
  },
  /**
   * Footer link columns
   */
  linkColumns: {
    type: Array,
    default: () => []
  },
  /**
   * Contact information
   */
  contactInfo: {
    type: Object,
    default: null
  },
  /**
   * Show newsletter signup
   */
  showNewsletter: {
    type: Boolean,
    default: true
  },
  /**
   * Partners list
   */
  partners: {
    type: Array,
    default: () => []
  },
  /**
   * Copyright text (English)
   */
  copyright: {
    type: String,
    default: () => `© ${new Date().getFullYear()} All rights reserved.`
  },
  /**
   * Copyright text (Arabic)
   */
  copyrightAr: {
    type: String,
    default: () => `© ${new Date().getFullYear()} جميع الحقوق محفوظة.`
  },
  /**
   * Bottom bar links
   */
  bottomLinks: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['newsletter-submit']);

const newsletterEmail = ref('');

const handleNewsletterSubmit = () => {
  emit('newsletter-submit', newsletterEmail.value);
  newsletterEmail.value = '';
};

// Logo URL computed property - Always use logo-primary for footer
const logoUrl = computed(() => {
  // Always use logo-primary.png in footer for both light and dark modes
  return '/images/logo/logo-primary.png';
});

// Handle logo loading error
const handleLogoError = (event) => {
  console.error('Logo failed to load');
  // Fallback to regular logo
  event.target.src = '/images/logo/logo.png';
};

// Social icon components
const getSocialIcon = (name) => {
  const icons = {
    facebook: {
      render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' })
        ]);
      }
    },
    twitter: {
      render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { d: 'M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z' })
        ]);
      }
    },
    instagram: {
      render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { d: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z' })
        ]);
      }
    },
    linkedin: {
      render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { d: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' })
        ]);
      }
    },
    youtube: {
      render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { d: 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' })
        ]);
      }
    },
    whatsapp: {
      render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { d: 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z' })
        ]);
      }
    }
  };
  
  return icons[name.toLowerCase()] || icons.facebook;
};
</script>

<style scoped>
.glass-footer-heading {
  font-size: 1rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 1.5rem;
  color: white;
}

.glass-footer-link {
  display: block;
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.7);
  transition: all 0.3s ease;
  position: relative;
}

.glass-footer-link:hover {
  color: #CBE4F8;
  padding-inline-start: 0.5rem;
}

.glass-footer-link::before {
  content: '';
  position: absolute;
  inset-inline-start: 0;
  top: 50%;
  transform: translateY(-50%) scaleX(0);
  width: 2px;
  height: 60%;
  background: #CBE4F8;
  transition: transform 0.3s ease;
  transform-origin: left;
}

[dir="rtl"] .glass-footer-link::before {
  transform-origin: right;
}

.glass-footer-link:hover::before {
  transform: translateY(-50%) scaleX(1);
}

.glass-social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  background: rgba(203, 228, 248, 0.1);
  border: 1px solid rgba(203, 228, 248, 0.2);
  border-radius: 0.75rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  color: rgba(255, 255, 255, 0.8);
}

.glass-social-link:hover {
  border-color: #CBE4F8;
  background: rgba(203, 228, 248, 0.2);
  color: white;
  transform: translateY(-2px);
}

.glass-social-link svg {
  position: relative;
  z-index: 1;
}

.glass-contact-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.25rem;
  height: 2.25rem;
  background: rgba(203, 228, 248, 0.15);
  border: 1px solid rgba(203, 228, 248, 0.2);
  border-radius: 0.5rem;
  flex-shrink: 0;
  color: #CBE4F8;
  transition: all 0.3s ease;
}

.glass-contact-icon:hover {
  background: rgba(203, 228, 248, 0.25);
  transform: scale(1.05);
}
</style>
