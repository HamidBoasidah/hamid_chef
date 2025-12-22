<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="currentPageTitle">
        <CreateBooking 
          :customers="customers"
          :chefs="chefs" 
          :addresses="addresses"
          :chefServices="chefServices"
        />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import AdminLayout from '@/Components/layout/AdminLayout.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import CreateBooking from '@/Components/admin/booking/CreateBooking.vue'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const { t } = useI18n()
const currentPageTitle = computed(() => t('booking.create_booking'))

const customers = computed(() => usePage().props.customers)
const chefs = computed(() => usePage().props.chefs)
const addresses = computed(() => usePage().props.addresses)
const chefServices = computed(() => usePage().props.chefServices)
</script>