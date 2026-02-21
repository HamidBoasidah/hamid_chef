<template>
  <ChefLayout>
    <PageBreadcrumb :pageTitle="t('chef_address.add_address')" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="t('chef_address.add_address')">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Label -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_address.label') }}
            </label>
            <input
              v-model="form.label"
              type="text"
              :placeholder="t('chef_address.label_placeholder')"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
            />
            <p v-if="form.errors.label" class="mt-1 text-sm text-red-600">{{ form.errors.label }}</p>
          </div>

          <!-- Governorate -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_address.governorate') }}
            </label>
            <select
              v-model="form.governorate_id"
              @change="onGovernorateChange"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
            >
              <option value="">{{ t('chef_address.select_governorate') }}</option>
              <option v-for="gov in governorates" :key="gov.id" :value="gov.id">
                {{ gov.name }}
              </option>
            </select>
            <p v-if="form.errors.governorate_id" class="mt-1 text-sm text-red-600">{{ form.errors.governorate_id }}</p>
          </div>

          <!-- District -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_address.district') }}
            </label>
            <select
              v-model="form.district_id"
              @change="onDistrictChange"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              :disabled="!form.governorate_id"
            >
              <option value="">{{ t('chef_address.select_district') }}</option>
              <option v-for="dist in filteredDistricts" :key="dist.id" :value="dist.id">
                {{ dist.name }}
              </option>
            </select>
            <p v-if="form.errors.district_id" class="mt-1 text-sm text-red-600">{{ form.errors.district_id }}</p>
          </div>

          <!-- Area -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_address.area') }}
            </label>
            <select
              v-model="form.area_id"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              :disabled="!form.district_id"
            >
              <option value="">{{ t('chef_address.select_area') }}</option>
              <option v-for="area in filteredAreas" :key="area.id" :value="area.id">
                {{ area.name }}
              </option>
            </select>
            <p v-if="form.errors.area_id" class="mt-1 text-sm text-red-600">{{ form.errors.area_id }}</p>
          </div>

          <!-- Address -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_address.address') }} *
            </label>
            <textarea
              v-model="form.address"
              rows="2"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              required
            ></textarea>
            <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
          </div>

          <!-- Street -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_address.street') }} *
            </label>
            <input
              v-model="form.street"
              type="text"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              required
            />
            <p v-if="form.errors.street" class="mt-1 text-sm text-red-600">{{ form.errors.street }}</p>
          </div>

          <!-- Building, Floor, Apartment -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ t('chef_address.building_number') }}
              </label>
              <input
                v-model="form.building_number"
                type="number"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ t('chef_address.floor_number') }}
              </label>
              <input
                v-model="form.floor_number"
                type="number"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ t('chef_address.apartment_number') }}
              </label>
              <input
                v-model="form.apartment_number"
                type="number"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              />
            </div>
          </div>

          <!-- Is Default -->
          <div class="flex items-center">
            <input
              v-model="form.is_default"
              type="checkbox"
              id="is_default"
              class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            />
            <label for="is_default" class="ltr:ml-2 rtl:mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ t('chef_address.is_default') }}
            </label>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3">
            <Link
              :href="route('chef.addresses.index')"
              class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              {{ t('common.cancel') }}
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 disabled:opacity-50 transition-colors"
            >
              {{ form.processing ? t('common.saving') : t('common.create') }}
            </button>
          </div>
        </form>
      </ComponentCard>
    </div>
  </ChefLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link, usePage, useForm } from '@inertiajs/vue3'
import ChefLayout from '@/Components/layout/ChefLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'

const { t } = useI18n()
const page = usePage()

const governorates = computed(() => page.props.governorates || [])
const districts = computed(() => page.props.districts || [])
const areas = computed(() => page.props.areas || [])

const form = useForm({
  label: '',
  governorate_id: '',
  district_id: '',
  area_id: '',
  address: '',
  street: '',
  building_number: '',
  floor_number: '',
  apartment_number: '',
  is_default: false,
})

const filteredDistricts = computed(() => {
  if (!form.governorate_id) return []
  return districts.value.filter(d => d.governorate_id === parseInt(form.governorate_id))
})

const filteredAreas = computed(() => {
  if (!form.district_id) return []
  return areas.value.filter(a => a.district_id === parseInt(form.district_id))
})

const onGovernorateChange = () => {
  form.district_id = ''
  form.area_id = ''
}

const onDistrictChange = () => {
  form.area_id = ''
}

const submit = () => {
  form.post(route('chef.addresses.store'))
}
</script>
