<template>
  <ChefLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="currentPageTitle">
        <EditChefService :service="service" :chefs="chefs" :tags="tags" />
      </ComponentCard>
    </div>
  </ChefLayout>
</template>

<script setup>
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ChefLayout from '@/Components/layout/ChefLayout.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import EditChefService from '@/Components/Chef/chef-service/EditChefService.vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const currentPageTitle = computed(() => t('chef_services.editChefService'))

const service = computed(() => usePage().props.service)
const chefs = computed(() => usePage().props.chefs)
const tags = computed(() => usePage().props.tags)

</script>