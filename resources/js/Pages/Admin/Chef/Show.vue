<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="currentPageTitle">
        <ShowChef :chef="chef" />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import ShowChef from '@/Components/admin/chef/ShowChef.vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'

const { t } = useI18n()
const currentPageTitle = computed(() => t('chefs.showChef'))

const chef = computed(() => usePage().props.chef)

</script>
