import { createI18n } from 'vue-i18n';
import en from '@/locales/en.json';
import ar from '@/locales/ar.json';
import { getSavedDirection } from '@/utils/direction';

// Determine initial locale from saved direction (rtl -> ar, ltr -> en)
const initialLocale = getSavedDirection() === 'rtl' ? 'ar' : 'en';

export const i18n = createI18n({
  legacy: false,
  locale: initialLocale,
  fallbackLocale: 'en',
  messages: { en, ar },
});

export function setHtmlLang(locale: string) {
  if (typeof document !== 'undefined') {
    document.documentElement.setAttribute('lang', locale);
  }
}
