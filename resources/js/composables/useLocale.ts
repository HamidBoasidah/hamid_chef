import axios from 'axios'; import { i18n } from '@/i18n';
import { route } from 'ziggy-js';
export async function switchLocale(l:'ar'|'en'){
  document.documentElement.lang = l;
  document.documentElement.dir  = l==='ar'?'rtl':'ltr';
  i18n.global.locale.value = l;
  await axios.post(route('locale.set'), { locale: l });
}