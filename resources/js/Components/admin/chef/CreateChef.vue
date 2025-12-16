<template>
  <div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('chefs.chefInformation') }}</h2>
      </div>
      <div class="p-4 sm:p-6 dark:border-gray-800">
        <form @submit.prevent>
          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.name') }}</label>
              <input v-model="form.name" type="text" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" :placeholder="t('chefs.namePlaceholder')" />
              <p v-if="form.errors.name" class="mt-1 text-sm text-error-500">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.user') }}</label>
              <select v-model="form.user_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option :value="null">{{ t('common.selectUser') }}</option>
                <option v-for="user in users" :key="user.id" :value="user.id">{{ `${user.first_name} ${user.last_name} - ${user.email}` }}</option>
              </select>
              <p v-if="form.errors.user_id" class="mt-1 text-sm text-error-500">{{ form.errors.user_id }}</p>
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.email') }}</label>
              <input v-model="form.email" type="text" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" :placeholder="t('chefs.emailPlaceholder')" />
              <p v-if="form.errors.email" class="mt-1 text-sm text-error-500">{{ form.errors.email }}</p>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.phoneNumber') }}</label>
              <div class="relative">
                <div class="absolute">
                  <select
                    v-model="selectedCountry"
                    class="appearance-none rounded-l-lg border-0 border-r border-gray-200 bg-transparent bg-none py-3 pl-3.5 pr-8 leading-tight text-gray-700 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:text-gray-400"
                  >
                    <option v-for="(code, country) in countryCodes" :key="country" :value="country">
                      {{ country }}
                    </option>
                  </select>
                  <div class="absolute inset-y-0 flex items-center text-gray-700 pointer-events-none right-3 dark:text-gray-400">
                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <input
                  v-model="form.phone"
                  :placeholder="t('chefs.phonePlaceholder')"
                  type="tel"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-3 pl-[84px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
              </div>
              <p v-if="form.errors.phone" class="mt-1 text-sm text-error-500">{{ form.errors.phone }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.address') }}</label>
              <input v-model="form.address" type="text" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" :placeholder="t('chefs.addressPlaceholder')" />
              <p v-if="form.errors.address" class="mt-1 text-sm text-error-500">{{ form.errors.address }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chefs.shortDescription') }}</label>
              <textarea v-model="form.short_description" rows="2" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" :placeholder="t('chefs.shortDescriptionPlaceholder')"></textarea>
              <p v-if="form.errors.short_description" class="mt-1 text-sm text-error-500">{{ form.errors.short_description }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chefs.longDescription') }}</label>
              <textarea v-model="form.long_description" rows="6" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" :placeholder="t('chefs.longDescriptionPlaceholder')"></textarea>
              <p v-if="form.errors.long_description" class="mt-1 text-sm text-error-500">{{ form.errors.long_description }}</p>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('governorates.governorate') }}</label>
              <select v-model="form.governorate_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option :value="null">{{ t('common.select') }}</option>
                <option v-for="g in governorates" :key="g.id" :value="g.id">{{ locale === 'ar' ? (g.name_ar ?? g.name_en) : (g.name_en ?? g.name_ar) }}</option>
              </select>
              <p v-if="form.errors.governorate_id" class="mt-1 text-sm text-error-500">{{ form.errors.governorate_id }}</p>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('districts.district') }}</label>
              <select v-model="form.district_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option :value="null">{{ t('common.select') }}</option>
                <option v-for="d in districts" :key="d.id" :value="d.id">{{ locale === 'ar' ? (d.name_ar ?? d.name_en) : (d.name_en ?? d.name_ar) }}</option>
              </select>
              <p v-if="form.errors.district_id" class="mt-1 text-sm text-error-500">{{ form.errors.district_id }}</p>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('areas.area') }}</label>
              <select v-model="form.area_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option :value="null">{{ t('common.select') }}</option>
                <option v-for="a in areas" :key="a.id" :value="a.id">{{ locale === 'ar' ? (a.name_ar ?? a.name_en) : (a.name_en ?? a.name_ar) }}</option>
              </select>
              <p v-if="form.errors.area_id" class="mt-1 text-sm text-error-500">{{ form.errors.area_id }}</p>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chefs.baseHourlyRate') }}</label>
              <input v-model="form.base_hourly_rate" type="number" step="0.01" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
              <p v-if="form.errors.base_hourly_rate" class="mt-1 text-sm text-error-500">{{ form.errors.base_hourly_rate }}</p>
            </div>

            <div class="md:col-span-1">
              <ImageUploadBox v-model="form.logo" input-id="logo-upload" label="chefs.logo" />
              <p v-if="form.errors.logo" class="mt-1 text-sm text-error-500">{{ form.errors.logo }}</p>
            </div>

            <div class="md:col-span-1">
              <ImageUploadBox v-model="form.banner" input-id="banner-upload" label="chefs.banner" />
              <p v-if="form.errors.banner" class="mt-1 text-sm text-error-500">{{ form.errors.banner }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.status') }}</label>
              <div class="mb-6 flex flex-wrap items-center gap-6 sm:gap-8">
                <div>
                  <label
                    for="toggle-active"
                    class="flex cursor-pointer select-none items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-400"
                  >
                    <div class="relative">
                      <input type="checkbox" id="toggle-active" class="sr-only" v-model="form.is_active" />
                      <div
                        class="block h-6 w-11 rounded-full"
                        :class="form.is_active ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'"
                      ></div>
                      <div
                        :class="form.is_active ? 'translate-x-full' : 'translate-x-0'"
                        class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-theme-sm duration-300 ease-linear"
                      ></div>
                    </div>
                    <span
                      class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                      :class="{
                        'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500': form.is_active,
                        'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500': !form.is_active,
                      }"
                    >
                      {{ form.is_active ? t('common.active') : t('common.inactive') }}
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
      <Link :href="route('admin.chefs.index')" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">{{ t('buttons.backToList') }}</Link>
      <button @click="create" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">{{ t('buttons.create') }}</button>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'
import { useNotifications } from '@/composables/useNotifications'
import ImageUploadBox from '@/Components/common/ImageUploadBox.vue'

const { t, locale } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({ roles: Array, users: Array, governorates: Array, districts: Array, areas: Array })

const form = useForm({
  user_id: null,
  name: '',
  email: '',
  phone: '',
  address: '',
  short_description: '',
  long_description: '',
  base_hourly_rate: 0,
  is_active: true,
  logo: null,
  banner: null,
  governorate_id: null,
  district_id: null,
  area_id: null,
})

function create() {
  form.post(route('admin.chefs.store'), {
    onSuccess: () => success(t('chefs.chefCreatedSuccessfully')),
    onError: () => error(t('chefs.chefCreationFailed')),
  })
}

// ImageUploadBox component handles previews and cleanup

const selectedCountry = ref('YE')
const countryCodes = {
  YE: '+967',
}
</script>
