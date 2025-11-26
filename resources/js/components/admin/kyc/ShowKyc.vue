<template>
  <div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('kyc.applicantSummary') }}</h2>
      </div>
      <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 gap-x-5 gap-y-6 md:grid-cols-2">
          <div class="flex items-center gap-4">
            <div class="h-14 w-14">
              <img v-if="kyc.user?.avatar" :src="`/storage/${kyc.user.avatar}`" class="h-14 w-14 rounded-full object-cover" alt="" />
              <UserCircleIcon v-else class="h-14 w-14 text-gray-400" />
            </div>
            <div class="flex flex-col">
              <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ applicantName }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ kyc.user?.email ?? t('kyc.noEmail') }}</p>
            </div>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.phoneNumber') }}</label>
            <p class="text-base text-gray-800 dark:text-white/90">{{ kyc.user?.phone_number ?? t('kyc.noPhone') }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.fullName') }}</label>
            <p class="text-base text-gray-800 dark:text-white/90">{{ kyc.full_name || applicantName }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.gender') }}</label>
            <p class="text-base capitalize text-gray-800 dark:text-white/90">{{ genderLabel }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.dateOfBirth') }}</label>
            <p class="text-base text-gray-800 dark:text-white/90">{{ displayDate(kyc.date_of_birth) }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.status') }}</label>
            <span :class="statusBadgeClass" class="inline-flex items-center justify-center gap-1 rounded-full px-3 py-1 text-xs font-medium">{{ statusLabel }}</span>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.verificationStatus') }}</label>
            <span :class="verificationBadgeClass" class="inline-flex items-center justify-center gap-1 rounded-full px-3 py-1 text-xs font-medium">
              {{ kyc.is_verified ? t('kyc.verified') : t('kyc.notVerified') }}
            </span>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.isActive') }}</label>
            <span :class="kyc.is_active ? 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400'" class="inline-flex items-center justify-center gap-1 rounded-full px-3 py-1 text-xs font-medium">
              {{ kyc.is_active ? t('common.active') : t('common.inactive') }}
            </span>
          </div>
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.address') }}</label>
            <p class="text-base text-gray-800 dark:text-white/90">{{ kyc.address || t('kyc.noAddressProvided') }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('kyc.documentDetails') }}</h2>
      </div>
      <div class="p-4 sm:p-6 space-y-4">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.documentType') }}</label>
            <p class="text-base text-gray-800 dark:text-white/90">{{ documentTypeLabel }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.verifiedAt') }}</label>
            <p class="text-base text-gray-800 dark:text-white/90">{{ displayDate(kyc.verified_at) }}</p>
          </div>
        </div>
        <div v-if="documentUrl" class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-900 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ t('kyc.secureDocument') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ documentFileName }}</p>
          </div>
          <a :href="documentUrl" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-white/10">
            {{ t('kyc.downloadDocument') }}
          </a>
        </div>
        <div v-else class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
          {{ t('kyc.noDocumentUploaded') }}
        </div>
        <div v-if="kyc.rejected_reason" class="rounded-lg border border-error-200 bg-error-50/40 px-4 py-3 text-sm text-error-700 dark:border-error-500/40 dark:bg-error-500/15 dark:text-error-200">
          <p class="font-medium">{{ t('kyc.rejectionReason') }}</p>
          <p>{{ kyc.rejected_reason }}</p>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('kyc.auditTrail') }}</h2>
      </div>
      <div class="p-4 sm:p-6 grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('datatable.createdAt') }}</label>
          <p class="text-base text-gray-800 dark:text-white/90">{{ displayDateTime(kyc.created_at) }}</p>
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('datatable.updatedAt') }}</label>
          <p class="text-base text-gray-800 dark:text-white/90">{{ displayDateTime(kyc.updated_at) }}</p>
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.createdBy') }}</label>
          <p class="text-base text-gray-800 dark:text-white/90">{{ kyc.created_by ?? '—' }}</p>
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('kyc.updatedBy') }}</label>
          <p class="text-base text-gray-800 dark:text-white/90">{{ kyc.updated_by ?? '—' }}</p>
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
      <Link :href="route('admin.kycs.index')" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
        {{ t('buttons.backToList') }}
      </Link>
      <Link :href="route('admin.kycs.edit', kyc.id)" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
        {{ t('buttons.edit') }}
      </Link>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { UserCircleIcon } from '@/icons'

const props = defineProps({
  kyc: {
    type: Object,
    required: true,
  },
})

const { t } = useI18n()

const kyc = computed(() => props.kyc ?? {})

const applicantName = computed(() => {
  if (kyc.value.full_name) return kyc.value.full_name
  const first = kyc.value.user?.first_name ?? ''
  const last = kyc.value.user?.last_name ?? ''
  const fallback = `${first} ${last}`.trim()
  return fallback || kyc.value.user?.name || t('kyc.unknownApplicant')
})

const genderLabel = computed(() => (kyc.value.gender ? t(`kyc.genders.${kyc.value.gender}`, kyc.value.gender) : '—'))

const statusLabel = computed(() => t(`kyc.statuses.${kyc.value.status ?? 'pending'}`))
const statusBadgeClass = computed(() => {
  switch (kyc.value.status) {
    case 'approved':
      return 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400'
    case 'rejected':
      return 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400'
    default:
      return 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400'
  }
})

const verificationBadgeClass = computed(() => (kyc.value.is_verified ? 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300'))

const documentTypeLabel = computed(() => (kyc.value.document_type ? t(`kyc.documentTypes.${kyc.value.document_type}`, kyc.value.document_type) : '—'))

const documentUrl = computed(() => (kyc.value.document_scan_copy ? `/storage/${kyc.value.document_scan_copy}` : null))
const documentFileName = computed(() => kyc.value.document_scan_copy?.split('/').pop() ?? t('kyc.noDocumentSelected'))

const displayDate = (value) => (value ? new Date(value).toLocaleDateString() : '—')
const displayDateTime = (value) => (value ? new Date(value).toLocaleString() : '—')
</script>
