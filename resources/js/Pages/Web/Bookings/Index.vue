<template>
  <WebLayout>
    <PageBreadcrumb :pageTitle="t('menu.bookings')" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="t('menu.bookings')">
        <p class="text-gray-600 dark:text-gray-300">{{ t('booking.bookings_management') }}</p>
      </ComponentCard>
    </div>
  </WebLayout>
</template>

<script setup>
import WebLayout from '@/Components/layout/WebLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
