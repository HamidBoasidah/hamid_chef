<template>
  <ChefLayout>
    <PageBreadcrumb :pageTitle="t('chef_kyc.title')" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="t('chef_kyc.my_kyc')">
        <!-- Add Button -->
        <div class="mb-6">
          <Link
            :href="route('chef.kyc.create')"
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
          >
            <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ t('chef_kyc.add_kyc') }}
          </Link>
        </div>

        <!-- KYC List -->
        <div v-if="kycs.length > 0" class="space-y-4">
          <div
            v-for="kyc in kycs"
            :key="kyc.id"
            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <span class="font-semibold text-gray-900 dark:text-white">
                    {{ getDocumentTypeLabel(kyc.document_type) }}
                  </span>
                  <span
                    :class="getStatusClass(kyc.status)"
                    class="px-2 py-1 text-xs font-medium rounded-full"
                  >
                    {{ getStatusLabel(kyc.status) }}
                  </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ t('chef_kyc.full_name') }}: {{ kyc.full_name }}
                </p>
                <p v-if="kyc.gender" class="text-sm text-gray-600 dark:text-gray-400">
                  {{ t('chef_kyc.gender') }}: {{ kyc.gender === 'male' ? t('common.male') : t('common.female') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                  {{ t('chef_kyc.submitted_at') }}: {{ kyc.created_at }}
                </p>
                <p v-if="kyc.rejected_reason" class="text-sm text-red-600 mt-2">
                  {{ t('chef_kyc.rejection_reason') }}: {{ kyc.rejected_reason }}
                </p>
              </div>
              <div class="flex items-center gap-2">
                <!-- View -->
                <Link
                  :href="route('chef.kyc.show', kyc.id)"
                  class="p-2 text-gray-600 hover:text-primary-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :title="t('common.view')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </Link>
                <!-- Edit (only for pending/rejected) -->
                <Link
                  v-if="['pending', 'rejected'].includes(kyc.status)"
                  :href="route('chef.kyc.edit', kyc.id)"
                  class="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :title="t('common.edit')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <!-- Download -->
                <a
                  v-if="kyc.document_scan_copy"
                  :href="route('chef.kyc.download', { kyc: kyc.id, type: 'document' })"
                  class="p-2 text-gray-600 hover:text-green-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :title="t('chef_kyc.download')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                </a>
                <!-- Delete (only for pending/rejected) -->
                <button
                  v-if="['pending', 'rejected'].includes(kyc.status)"
                  @click="confirmDelete(kyc)"
                  class="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :title="t('common.delete')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ t('chef_kyc.no_kyc') }}</h3>
          <p class="mt-1 text-sm text-gray-500">{{ t('chef_kyc.no_kyc_description') }}</p>
        </div>
      </ComponentCard>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      v-model="showDeleteModal"
      :title="t('messages.areYouSure')"
      :message="t('chef_kyc.delete_confirmation')"
      :confirmText="t('common.delete')"
      :cancelText="t('common.cancel')"
      :loading="isDeleting"
      :loadingText="t('common.loading')"
      @confirm="deleteKyc"
    />
  </ChefLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link, usePage, router } from '@inertiajs/vue3'
import ChefLayout from '@/Components/layout/ChefLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import ConfirmModal from '@/Components/modals/ConfirmModal.vue'

const { t } = useI18n()
const page = usePage()

const kycs = computed(() => page.props.kycs || [])

const showDeleteModal = ref(false)
const kycToDelete = ref(null)
const isDeleting = ref(false)

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

const confirmDelete = (kyc) => {
  kycToDelete.value = kyc
  showDeleteModal.value = true
}

const deleteKyc = () => {
  if (kycToDelete.value) {
    isDeleting.value = true
    router.delete(route('chef.kyc.destroy', kycToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        kycToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      },
    })
  }
}
</script>
