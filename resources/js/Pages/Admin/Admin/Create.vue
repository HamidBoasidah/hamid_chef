<template>
  <admin-layout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <AddAdminForm
      :roles="roles" />
  </admin-layout>
</template>

<script setup>
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import AddAdminForm from '@/Components/admin/admin/CreateAdmin.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'


const { t } = useI18n()
const currentPageTitle = computed(() => t('admins.addAdmin'))

// استلام البيانات من Inertia

const roles = computed(() => usePage().props.roles)

</script>
