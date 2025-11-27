<template>
  <admin-layout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <ShowAdminForm :admin="admin"/>
  </admin-layout>
</template>

<script setup>
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ShowAdminForm from '@/Components/admin/admin/ShowAdmin.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'


const { t } = useI18n()
const currentPageTitle = computed(() => t('admins.showAdmin'))

// استلام البيانات من Inertia

const admin = computed(() => usePage().props.admin)

</script>
