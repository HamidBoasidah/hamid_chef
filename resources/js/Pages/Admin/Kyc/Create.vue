<template>
  <admin-layout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <CreateKycForm
      :users="users"
    />
  </admin-layout>
</template>

<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import CreateKycForm from '@/components/admin/kyc/CreateKyc.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'

const { t } = useI18n()
const currentPageTitle = computed(() => t('kyc.addKyc'))

const users = computed(() => usePage().props.users ?? [])
</script>
