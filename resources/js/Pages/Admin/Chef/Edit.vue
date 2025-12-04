<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="currentPageTitle">
        <EditChef :chef="chef" 
        :governorates="governorates" 
        :districts="districts" 
        :areas="areas" 
        :users="users" />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import EditChef from '@/Components/admin/chef/EditChef.vue'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const { t } = useI18n()
const currentPageTitle = computed(() => t('chefs.editChef'))

const chef = computed(() => usePage().props.chef)
const governorates = computed(() => usePage().props.governorates)
const districts = computed(() => usePage().props.districts)
const areas = computed(() => usePage().props.areas)
const users = computed(() => usePage().props.users)

</script>
