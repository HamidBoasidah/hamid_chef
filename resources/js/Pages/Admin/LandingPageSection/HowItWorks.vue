<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="pageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="pageTitle" :desc="t('landingSections.sectionInfo')">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('landingSections.howItWorksSection') }}</p>
        <HowItWorks :section="section" @refresh="refreshSection" />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue';
import ComponentCard from '@/Components/common/ComponentCard.vue';
import AdminLayout from '@/Components/layout/AdminLayout.vue';
import HowItWorks from '@/Components/admin/landing-page-section/HowItWorks.vue';

const { t } = useI18n();

defineProps({
  section: {
    type: Object,
    required: true
  }
});

const pageTitle = computed(() => t('landingSections.howItWorksSection') || 'How It Works');

const refreshSection = () => {
  console.log('🔄 HowItWorks: Refreshing section data...')
  router.reload();
};
</script>
