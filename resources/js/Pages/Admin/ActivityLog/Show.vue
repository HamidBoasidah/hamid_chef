<template>
  <admin-layout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <ShowActivityLogForm :activity="activity"/>
  </admin-layout>
</template>

<script setup>
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ShowActivityLogForm from '@/Components/admin/activityLog/ShowActivityLog.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'


const { t } = useI18n()
const currentPageTitle = computed(() => t('activitylogs.showActivityLog'))

// استلام البيانات من Inertia

const activity = computed(() => usePage().props.activity)

</script>
