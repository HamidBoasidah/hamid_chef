import { createI18n } from 'vue-i18n';
import en from '@/locales/en.json';
import ar from '@/locales/ar.json';
import { getSavedDirection } from '@/utils/direction';

// Determine initial locale from saved direction (rtl -> ar, ltr -> en)
// Default to Arabic ('ar') if no saved direction or in SSR
const getInitialLocale = (): 'ar' | 'en' => {
  if (typeof window === 'undefined') return 'ar' // SSR default to Arabic
  const savedDirection = getSavedDirection()
  // Default to Arabic (RTL) if no saved direction
  return savedDirection === 'ltr' ? 'en' : 'ar'
}

const initialLocale = getInitialLocale()

export const i18n = createI18n({
  legacy: false,
  locale: initialLocale,
  fallbackLocale: 'ar', // Default fallback to Arabic
  messages: { en, ar },
});

export function setHtmlLang(locale: string) {
  if (typeof document !== 'undefined') {
    document.documentElement.setAttribute('lang', locale);
  }
}
