<template>
  <ChefLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="currentPageTitle">
        <ShowBookings :bookings="bookings" />
      </ComponentCard>
    </div>
  </ChefLayout>
</template>

<script setup>
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ChefLayout from '@/Components/layout/ChefLayout.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import ShowBookings from '@/Components/admin/booking/ShowBookings.vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const currentPageTitle = computed(() => t('menu.bookings'))

const bookings = computed(() => usePage().props.bookings)
</script>