<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="pageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="pageTitle" :desc="t('landingSections.visionMissionDescription')">
        <VisionMission :section="section" @refresh="refreshSection" />
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
import VisionMission from '@/Components/admin/landing-page-section/VisionMission.vue';

const { t } = useI18n();

defineProps({
  section: {
    type: Object,
    required: true
  }
});

const pageTitle = computed(() => t('landingSections.visionMissionValues') || 'Vision & Mission');

const refreshSection = () => {
  console.log('🔄 VisionMission: Refreshing section data...')
  router.reload();
};
</script>
