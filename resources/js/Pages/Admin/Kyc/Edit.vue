<template>
  <admin-layout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <EditKycForm
      :kyc="kyc"
      :users="users"
    />
  </admin-layout>
</template>

<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import EditKycForm from '@/components/admin/kyc/EditKyc.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'

const { t } = useI18n()
const currentPageTitle = computed(() => t('kyc.editKyc'))

const kyc = computed(() => usePage().props.kyc)
const users = computed(() => usePage().props.users ?? [])
</script>
