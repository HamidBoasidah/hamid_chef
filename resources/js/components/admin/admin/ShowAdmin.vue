<template>
  <div class="mx-auto max-w-3xl">
    <div class="bg-white p-6 rounded-lg shadow-sm dark:bg-gray-800">
      <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ admin.name }}</h3>
      <p class="text-sm text-gray-500">{{ admin.email }}</p>

      <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
          <dt class="text-sm font-medium text-gray-500">{{ t('common.phoneNumber') }}</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ admin.phone_number || '-' }}</dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500">{{ t('common.role') }}</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ admin.role?.name || '-' }}</dd>
        </div>
        <div class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-500">{{ t('common.createdAt') }}</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ admin.created_at }}</dd>
        </div>
      </div>

      <div class="mt-6 flex items-center gap-3">
        <inertia-link :href="route('admin.admins.index')" class="text-sm text-gray-600 hover:underline">{{ t('common.back') }}</inertia-link>
        <inertia-link :href="route('admin.admins.edit', admin.id)" class="text-sm text-white bg-brand-500 px-3 py-1 rounded">{{ t('common.edit') }}</inertia-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { route } from '@/route'

const props = defineProps({ admin: Object })
const admin = props.admin || {}
const { t } = useI18n()
</script>
