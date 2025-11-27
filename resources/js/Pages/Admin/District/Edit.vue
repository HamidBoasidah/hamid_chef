<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <EditDistrict
    :district="district"
    :governorates="governorates"/>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import EditDistrict from '@/Components/admin/district/EditDistrict.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'

const { t } = useI18n()
const currentPageTitle = computed(() => t('districts.editDistrict'))

const district = computed(() => usePage().props.district)

const governorates = computed(() => usePage().props.governorates)
</script>
