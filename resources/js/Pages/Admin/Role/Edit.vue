<template>
  <admin-layout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <EditRoleForm :role="role" :acl="acl" />
  </admin-layout>
</template>

<script setup>
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import EditRoleForm from '@/Components/admin/role/EditRole.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'

const { t } = useI18n()
const currentPageTitle = computed(() => t('roles.editRole'))

const role = computed(() => usePage().props.role)
const acl = computed(() => usePage().props.acl)
</script>
