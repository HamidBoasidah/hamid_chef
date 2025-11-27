<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Spinner 3">
        <SpinnerThree />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import SpinnerThree from '@/Components/spinner/SpinnerThree.vue'
const currentPageTitle = ref('Spinners')
</script>

<style></style>
