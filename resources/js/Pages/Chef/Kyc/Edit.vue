<template>
  <ChefLayout>
    <PageBreadcrumb :pageTitle="t('chef_kyc.edit_kyc')" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="t('chef_kyc.edit_kyc')">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Document Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_kyc.document_type') }} *
            </label>
            <select
              v-model="form.type"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              required
            >
              <option value="">{{ t('common.select') }}</option>
              <option v-for="type in kycTypes" :key="type.value" :value="type.value">
                {{ type.label }}
              </option>
            </select>
            <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
          </div>

          <!-- Document Number -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_kyc.full_name') }} *
            </label>
            <input
              v-model="form.document_number"
              type="text"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
              required
            />
            <p v-if="form.errors.document_number" class="mt-1 text-sm text-red-600">{{ form.errors.document_number }}</p>
          </div>

          <!-- Current Document Front -->
          <div v-if="kyc.document_front" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('chef_kyc.document_scan') }}</p>
            <img :src="`/storage/${kyc.document_front}`" class="h-32 object-contain rounded-lg" />
          </div>

          <!-- Document Front -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('chef_kyc.upload_document') }}
            </label>
            <input
              type="file"
              @change="handleFileChange($event, 'document_front')"
              accept="image/*,.pdf"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
            />
            <p class="mt-1 text-xs text-gray-500">{{ t('chef_kyc.allowed_formats') }}</p>
            <p v-if="form.errors.document_front" class="mt-1 text-sm text-red-600">{{ form.errors.document_front }}</p>
            <img v-if="previews.document_front" :src="previews.document_front" class="mt-2 h-32 object-contain rounded-lg" />
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3">
            <Link
              :href="route('chef.kyc.index')"
              class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              {{ t('common.cancel') }}
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 disabled:opacity-50 transition-colors"
            >
              {{ form.processing ? t('common.saving') : t('common.update') }}
            </button>
          </div>
        </form>
      </ComponentCard>
    </div>
  </ChefLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link, usePage, useForm } from '@inertiajs/vue3'
import ChefLayout from '@/Components/layout/ChefLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'

const { t } = useI18n()
const page = usePage()

const kyc = computed(() => page.props.kyc || {})
const kycTypes = computed(() => page.props.kycTypes || [])

const form = useForm({
  type: kyc.value.type || '',
  document_number: kyc.value.document_number || '',
  document_front: null,
  document_back: null,
})

const previews = ref({
  document_front: null,
  document_back: null,
})

const handleFileChange = (event, field) => {
  const file = event.target.files[0]
  if (file) {
    form[field] = file
    if (file.type.startsWith('image/')) {
      previews.value[field] = URL.createObjectURL(file)
    } else {
      previews.value[field] = null
    }
  }
}

const submit = () => {
  form.post(route('chef.kyc.update', kyc.value.id), {
    forceFormData: true,
    _method: 'PUT',
  })
}
</script>
