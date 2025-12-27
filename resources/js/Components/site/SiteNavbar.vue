<template>
  <nav 
    ref="navbarRef"
    :class="navbarClasses"
    role="navigation"
    :aria-label="ariaLabel"
  >
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20 lg:h-24">
        
        <!-- Logo -->
        <div class="flex-shrink-0 z-50">
          <slot name="logo">
            <Link href="/" class="group">
              <div class="relative">
                <!-- Light Mode Logo -->
                <img 
                  v-if="logoUrl" 
                  :src="logoUrl" 
                  :alt="logoAlt" 
                  class="h-10 sm:h-12 lg:h-14 w-auto transition-transform duration-300 group-hover:scale-105 dark:hidden"
                  @error="handleImageError"
                />
                <!-- Dark Mode Logo -->
                <img 
                  v-if="darkLogoUrl" 
                  :src="darkLogoUrl" 
                  :alt="logoAlt" 
                  class="h-10 sm:h-12 lg:h-14 w-auto transition-transform duration-300 group-hover:scale-105 hidden dark:block"
                  @error="handleDarkImageError"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-primary-600/20 rounded-lg blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
            </Link>
          </slot>
        </div>
        
        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center gap-1">
          <slot name="nav-items">
            <template v-for="item in navItems" :key="item.href">
              <!-- Dropdown Menu for Products (Hierarchical) -->
              <div v-if="item.children && item.children.length" class="relative group">
                <button
                  :class="[
                    'glass-nav-link flex items-center gap-1',
                    { 'active': isActive(item.href) }
                  ]"
                >
                  {{ currentLang === 'ar' ? item.label_ar : item.label_en }}
                </button>
                <!-- Main Dropdown -->
                <div class="glass-dropdown">
                  <template v-for="child in item.children" :key="child.href">
                    <!-- Item with sub-children -->
                    <div v-if="child.children && child.children.length" class="relative group/sub">
                      <a :href="child.href" class="glass-dropdown-item">
                        {{ currentLang === 'ar' ? child.label_ar : child.label_en }}
                      </a>
                      <!-- Sub Dropdown -->
                      <div class="glass-sub-dropdown">
                        <a v-for="subChild in child.children" :key="subChild.href" :href="subChild.href" class="glass-dropdown-item">
                          {{ currentLang === 'ar' ? subChild.label_ar : subChild.label_en }}
                        </a>
                      </div>
                    </div>
                    <!-- Simple item -->
                    <a v-else :href="child.href" class="glass-dropdown-item">
                      {{ currentLang === 'ar' ? child.label_ar : child.label_en }}
                    </a>
                  </template>
                </div>
              </div>
              <!-- Regular Link -->
              <Link
                v-else-if="!item.href.startsWith('#')"
                :href="item.href"
                :class="[
                  'glass-nav-link',
                  { 'active': isActive(item.href) }
                ]"
              >
                {{ currentLang === 'ar' ? item.label_ar : item.label_en }}
              </Link>
              <!-- Anchor Link -->
              <a
                v-else
                :href="item.href"
                :class="[
                  'glass-nav-link',
                  { 'active': isActive(item.href) }
                ]"
              >
                {{ currentLang === 'ar' ? item.label_ar : item.label_en }}
              </a>
            </template>
          </slot>
        </div>
        
        <!-- Right Side Actions -->
        <div class="flex items-center gap-2 lg:gap-3 z-50">
          <!-- Language Switcher -->
          <slot name="language-switcher">
            <button
              v-if="showLanguageSwitcher"
              @click="toggleLanguage"
              class="glass-action-btn group"
              :aria-label="currentLang === 'ar' ? 'Switch to English' : 'التبديل إلى العربية'"
            >
              <span class="relative flex items-center gap-1.5 font-semibold text-sm">
                <svg class="w-4 h-4 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                </svg>
                {{ currentLang === 'ar' ? 'EN' : 'عربي' }}
              </span>
            </button>
          </slot>
          
          <!-- Dark Mode Toggle -->
          <slot name="dark-mode-toggle">
            <button
              v-if="showDarkModeToggle"
              @click="toggleDarkMode"
              class="glass-action-btn group"
              :aria-label="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
              <svg v-if="isDarkMode" class="w-5 h-5 transition-transform group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <svg v-else class="w-5 h-5 transition-transform group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </button>
          </slot>
          
          <!-- Mobile Menu Button -->
          <button
            @click="toggleMobileMenu"
            class="lg:hidden glass-mobile-menu-btn group"
            :class="{ 'active': isMobileMenuOpen }"
            :aria-expanded="isMobileMenuOpen"
            aria-controls="mobile-menu"
            aria-label="Toggle navigation menu"
          >
            <span class="hamburger-line top"></span>
            <span class="hamburger-line middle"></span>
            <span class="hamburger-line bottom"></span>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div
        v-if="isMobileMenuOpen"
        id="mobile-menu"
        class="lg:hidden glass-mobile-menu"
      >
        <div class="container mx-auto px-4 py-4 space-y-2">
          <slot name="mobile-nav-items">
            <template v-for="item in navItems" :key="'mobile-' + item.href">
              <Link
                v-if="!item.href.startsWith('#')"
                :href="item.href"
                :class="[
                  'glass-mobile-nav-link',
                  { 'active': isActive(item.href) }
                ]"
                @click="closeMobileMenu"
              >
                {{ currentLang === 'ar' ? item.label_ar : item.label_en }}
              </Link>
              <a
                v-else
                :href="item.href"
                :class="[
                  'glass-mobile-nav-link',
                  { 'active': isActive(item.href) }
                ]"
                @click="closeMobileMenu"
              >
                {{ currentLang === 'ar' ? item.label_ar : item.label_en }}
              </a>
            </template>
          </slot>
        </div>
      </div>
    </Transition>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  /**
   * Site logo URL
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
   * Site name (shown next to logo)
   */
  siteName: {
    type: String,
    default: null
  },
  /**
   * Site name in Arabic
   */
  siteNameAr: {
    type: String,
    default: null
  },
  /**
   * Navigation items array
   */
  navItems: {
    type: Array,
    default: () => []
  },
  /**
   * Current language
   */
  currentLang: {
    type: String,
    default: 'en'
  },
  /**
   * Show language switcher
   */
  showLanguageSwitcher: {
    type: Boolean,
    default: true
  },
  /**
   * Show dark mode toggle
   */
  showDarkModeToggle: {
    type: Boolean,
    default: true
  },
  /**
   * Dark mode state
   */
  isDarkMode: {
    type: Boolean,
    default: false
  },
  /**
   * CTA button text (English)
   */
  ctaText: {
    type: String,
    default: null
  },
  /**
   * CTA button text (Arabic)
   */
  ctaTextAr: {
    type: String,
    default: null
  },
  /**
   * CTA button href
   */
  ctaHref: {
    type: String,
    default: '#contact'
  },
  /**
   * Current route/path for active state
   */
  currentPath: {
    type: String,
    default: '/'
  },
  /**
   * Aria label for navigation
   */
  ariaLabel: {
    type: String,
    default: 'Main navigation'
  },
  /**
   * Enable transparent mode (no background until scroll)
   */
  transparent: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['toggle-language', 'toggle-dark-mode']);

const navbarRef = ref(null);
const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);

const navbarClasses = computed(() => [
  'glass-navbar',
  {
    'glass-navbar-scrolled': isScrolled.value || !props.transparent,
    'glass-navbar-transparent': props.transparent && !isScrolled.value
  }
]);

const logoUrl = computed(() => {
  if (!props.logo) {
    // Default to logo.png for light mode
    return '/images/logo/logo.png';
  }
  
  // If logo starts with http or https, return as is
  if (props.logo.startsWith('http://') || props.logo.startsWith('https://')) {
    return props.logo;
  }
  
  // If logo starts with /images/ or /storage/, return as is
  if (props.logo.startsWith('/images/') || props.logo.startsWith('/storage/')) {
    return props.logo;
  }
  
  // If logo is just a filename, check if it's in images/logo first
  if (props.logo.includes('logo')) {
    return `/images/logo/${props.logo}`;
  }
  
  // Otherwise, prepend /storage/
  return `/storage/${props.logo}`;
});

const darkLogoUrl = computed(() => {
  // Always use logo-dark.png for dark mode
  return '/images/logo/logo-dark.png';
});

const displaySiteName = computed(() => {
  if (props.currentLang === 'ar' && props.siteNameAr) {
    return props.siteNameAr;
  }
  return props.siteName;
});

const handleImageError = (event) => {
  console.error('Logo failed to load:', props.logo);
  // Fallback to logo.png
  event.target.src = '/images/logo/logo.png';
};

const handleDarkImageError = (event) => {
  console.error('Dark logo failed to load');
  // Fallback to regular logo
  event.target.src = '/images/logo/logo.png';
};

const isActive = (href) => {
  if (href === '/') {
    return props.currentPath === '/';
  }
  return props.currentPath.startsWith(href);
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
};

const toggleLanguage = () => {
  emit('toggle-language');
};

const toggleDarkMode = () => {
  emit('toggle-dark-mode');
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.glass-navbar {
  position: sticky;
  top: 0;
  z-index: 1000;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.glass-navbar-transparent {
  background: transparent;
  border-bottom: none;
}

.glass-navbar-scrolled {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
}

.dark .glass-navbar-scrolled {
  background: #1f2937;
  border-bottom-color: #374151;
}

.glass-nav-link {
  position: relative;
  padding: 0.625rem 1.125rem;
  font-weight: 600;
  font-size: 0.9375rem;
  color: inherit;
  text-decoration: none;
  border-radius: 0.625rem;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  letter-spacing: -0.01em;
}

.glass-nav-link:hover {
  color: #083064;
}

.dark .glass-nav-link:hover {
  color: #CBE4F8;
}

.glass-nav-link::after {
  content: '';
  position: absolute;
  bottom: 0.25rem;
  left: 50%;
  width: 0;
  height: 2.5px;
  background: #083064;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  transform: translateX(-50%);
  border-radius: 2px;
}

.glass-nav-link:hover::after,
.glass-nav-link.active::after {
  width: 70%;
}

.glass-nav-link.active {
  color: #083064;
  font-weight: 700;
}

.dark .glass-nav-link.active {
  color: #CBE4F8;
  font-weight: 700;
}

.glass-mobile-menu {
  background: #ffffff;
  border-top: 1px solid #e5e7eb;
  max-height: calc(100vh - 5rem);
  overflow-y: auto;
}

.dark .glass-mobile-menu {
  background: #1f2937;
  border-top-color: #374151;
}

.glass-mobile-nav-link {
  display: block;
  padding: 1rem 1.25rem;
  font-weight: 600;
  font-size: 1rem;
  color: inherit;
  text-decoration: none;
  border-radius: 0.75rem;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border-left: 3px solid transparent;
}

[dir="rtl"] .glass-mobile-nav-link {
  border-left: none;
  border-right: 3px solid transparent;
}

.glass-mobile-nav-link:hover {
  background: #CBE4F8;
  color: #083064;
  border-left-color: #083064;
  transform: translateX(4px);
}

[dir="rtl"] .glass-mobile-nav-link:hover {
  transform: translateX(-4px);
  border-right-color: #083064;
}

.dark .glass-mobile-nav-link:hover {
  background: #083064;
  color: #CBE4F8;
  border-left-color: #CBE4F8;
}

[dir="rtl"] .dark .glass-mobile-nav-link:hover {
  border-right-color: #CBE4F8;
}

.glass-mobile-nav-link.active {
  background: #CBE4F8;
  color: #083064;
  border-left-color: #083064;
}

[dir="rtl"] .glass-mobile-nav-link.active {
  border-left-color: transparent;
  border-right-color: #083064;
}

.dark .glass-mobile-nav-link.active {
  background: #083064;
  color: #CBE4F8;
  border-left-color: #CBE4F8;
}

[dir="rtl"] .dark .glass-mobile-nav-link.active {
  border-right-color: #CBE4F8;
}

/* Dropdown Styles */
.glass-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 0;
  min-width: 300px;
  padding: 0.625rem;
  background: #ffffff;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 100;
}

.dark .glass-dropdown {
  background: #1f2937;
  border-color: #374151;
}

.group:hover .glass-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.glass-dropdown-item {
  display: block;
  padding: 0.875rem 1.125rem;
  font-size: 0.9375rem;
  font-weight: 500;
  color: inherit;
  text-decoration: none;
  border-radius: 0.625rem;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.glass-dropdown-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 3px;
  height: 100%;
  background: #083064;
  transform: scaleY(0);
  transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 0 2px 2px 0;
}

[dir="rtl"] .glass-dropdown-item::before {
  left: auto;
  right: 0;
  border-radius: 2px 0 0 2px;
}

.glass-dropdown-item:hover {
  background: #CBE4F8;
  color: #083064;
  padding-left: 1.375rem;
}

[dir="rtl"] .glass-dropdown-item:hover {
  padding-left: 1.125rem;
  padding-right: 1.375rem;
}

.glass-dropdown-item:hover::before {
  transform: scaleY(1);
}

.dark .glass-dropdown-item:hover {
  background: #083064;
  color: #CBE4F8;
}

[dir="rtl"] .glass-dropdown {
  left: auto;
  right: 0;
}

/* Sub Dropdown (nested) */
.glass-sub-dropdown {
  position: absolute;
  top: 0;
  left: calc(100% + 0.5rem);
  min-width: 280px;
  padding: 0.625rem;
  background: #ffffff;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  opacity: 0;
  visibility: hidden;
  transform: translateX(-8px);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 101;
}

.dark .glass-sub-dropdown {
  background: #1f2937;
  border-color: #374151;
}

.group\/sub:hover .glass-sub-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}

[dir="rtl"] .glass-sub-dropdown {
  left: auto;
  right: calc(100% + 0.5rem);
  transform: translateX(8px);
}

[dir="rtl"] .group\/sub:hover .glass-sub-dropdown {
  transform: translateX(0);
}

/* Action Buttons */
.glass-action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.625rem 0.875rem;
  background: transparent;
  border: 2px solid transparent;
  border-radius: 0.5rem;
  color: inherit;
  font-weight: 500;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.glass-action-btn:hover {
  opacity: 0.8;
}

.dark .glass-action-btn {
  color: inherit;
}

.dark .glass-action-btn:hover {
  opacity: 0.8;
}

/* Mobile Menu Button (Hamburger) */
.glass-mobile-menu-btn {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 2.75rem;
  height: 2.75rem;
  padding: 0.5rem;
  background: #CBE4F8;
  border: 2px solid #083064;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.glass-mobile-menu-btn:hover {
  background: #083064;
  color: #CBE4F8;
}

.dark .glass-mobile-menu-btn {
  background: #083064;
  border-color: #CBE4F8;
  color: #CBE4F8;
}

.dark .glass-mobile-menu-btn:hover {
  background: #CBE4F8;
  color: #083064;
}

.hamburger-line {
  display: block;
  width: 1.25rem;
  height: 2px;
  background: currentColor;
  border-radius: 2px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: absolute;
}

.hamburger-line.top {
  top: 0.625rem;
}

.hamburger-line.middle {
  top: 50%;
  transform: translateY(-50%);
}

.hamburger-line.bottom {
  bottom: 0.625rem;
}

.glass-mobile-menu-btn.active .hamburger-line.top {
  top: 50%;
  transform: translateY(-50%) rotate(45deg);
}

.glass-mobile-menu-btn.active .hamburger-line.middle {
  opacity: 0;
  transform: translateY(-50%) translateX(-10px);
}

.glass-mobile-menu-btn.active .hamburger-line.bottom {
  bottom: 50%;
  transform: translateY(50%) rotate(-45deg);
}

/* Mobile Menu Scrollbar */
.glass-mobile-menu::-webkit-scrollbar {
  width: 6px;
}

.glass-mobile-menu::-webkit-scrollbar-track {
  background: transparent;
}

.glass-mobile-menu::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}

.dark .glass-mobile-menu::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
}

.glass-mobile-menu::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.3);
}

.dark .glass-mobile-menu::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Responsive Improvements */
@media (max-width: 640px) {
  .glass-action-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }
}
</style>
