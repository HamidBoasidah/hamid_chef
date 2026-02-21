<template>
  <ChefLayout>
    <PageBreadcrumb :pageTitle="t('chef_kyc.view_kyc')" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="t('chef_kyc.view_kyc')">
        <!-- Status Badge -->
        <div class="mb-6">
          <span
            :class="getStatusClass(kyc.status)"
            class="px-3 py-1.5 text-sm font-medium rounded-full"
          >
            {{ getStatusLabel(kyc.status) }}
          </span>
        </div>

        <!-- KYC Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Document Type -->
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_kyc.document_type') }}</p>
            <p class="mt-1 text-gray-900 dark:text-white">{{ getDocumentTypeLabel(kyc.document_type) }}</p>
          </div>

          <!-- Full Name -->
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_kyc.full_name') }}</p>
            <p class="mt-1 text-gray-900 dark:text-white">{{ kyc.full_name }}</p>
          </div>

          <!-- Gender -->
          <div v-if="kyc.gender">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_kyc.gender') }}</p>
            <p class="mt-1 text-gray-900 dark:text-white">{{ kyc.gender === 'male' ? t('common.male') : t('common.female') }}</p>
          </div>

          <!-- Date of Birth -->
          <div v-if="kyc.date_of_birth">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_kyc.date_of_birth') }}</p>
            <p class="mt-1 text-gray-900 dark:text-white">{{ kyc.date_of_birth }}</p>
          </div>

          <!-- Address -->
          <div v-if="kyc.address" class="md:col-span-2">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_kyc.address') }}</p>
            <p class="mt-1 text-gray-900 dark:text-white">{{ kyc.address }}</p>
          </div>

          <!-- Submitted At -->
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_kyc.submitted_at') }}</p>
            <p class="mt-1 text-gray-900 dark:text-white">{{ kyc.created_at }}</p>
          </div>

          <!-- Verified At -->
          <div v-if="kyc.verified_at">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_kyc.verified_at') }}</p>
            <p class="mt-1 text-gray-900 dark:text-white">{{ kyc.verified_at }}</p>
          </div>
        </div>

        <!-- Rejection Reason -->
        <div v-if="kyc.rejected_reason" class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
          <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ t('chef_kyc.rejection_reason') }}</p>
          <p class="mt-1 text-red-700 dark:text-red-400">{{ kyc.rejected_reason }}</p>
        </div>

        <!-- Document Scan -->
        <div class="mt-6 space-y-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ t('chef_kyc.document_scan') }}</h3>

          <div v-if="kyc.document_scan_copy" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <img :src="`/storage/${kyc.document_scan_copy}`" class="w-full max-w-md h-48 object-contain rounded-lg bg-gray-100 dark:bg-gray-800" />
            <a
              :href="route('chef.kyc.download', { kyc: kyc.id, type: 'document' })"
              class="mt-2 inline-flex items-center text-sm text-primary-600 hover:text-primary-700"
            >
              <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              {{ t('chef_kyc.download') }}
            </a>
          </div>
        </div>

        <!-- Certificates -->
        <div v-if="kyc.certificates && kyc.certificates.length > 0" class="mt-6 space-y-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ t('chef_kyc.certificates') }}</h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="(cert, index) in kyc.certificates" :key="index" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
              <img :src="`/storage/${cert}`" class="w-full h-32 object-contain rounded-lg bg-gray-100 dark:bg-gray-800" />
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end gap-3">
          <Link
            :href="route('chef.kyc.index')"
            class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            {{ t('common.backToList') }}
          </Link>
          <Link
            v-if="['pending', 'rejected'].includes(kyc.status)"
            :href="route('chef.kyc.edit', kyc.id)"
            class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
          >
            {{ t('common.edit') }}
          </Link>
        </div>
      </ComponentCard>
    </div>
  </ChefLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link, usePage } from '@inertiajs/vue3'
import ChefLayout from '@/Components/layout/ChefLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'

const { t } = useI18n()
const page = usePage()

const kyc = computed(() => page.props.kyc || {})

const getDocumentTypeLabel = (type) => {
  const types = {
    id_card: t('chef_kyc.document_types.id_card'),
    passport: t('chef_kyc.document_types.passport'),
    driving_license: t('chef_kyc.document_types.driving_license'),
  }
  return types[type] || type
}

const getStatusLabel = (status) => {
  const statuses = {
    pending: t('chef_kyc.statuses.pending'),
    approved: t('chef_kyc.statuses.approved'),
    rejected: t('chef_kyc.statuses.rejected'),
  }
  return statuses[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    approved: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
