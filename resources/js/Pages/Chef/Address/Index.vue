<template>
  <ChefLayout>
    <PageBreadcrumb :pageTitle="t('chef_address.title')" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard :title="t('chef_address.my_addresses')">
        <!-- Add Button -->
        <div class="mb-6">
          <Link
            :href="route('chef.addresses.create')"
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
          >
            <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ t('chef_address.add_address') }}
          </Link>
        </div>

        <!-- Addresses List -->
        <div v-if="addresses.length > 0" class="space-y-4">
          <div
            v-for="address in addresses"
            :key="address.id"
            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
            :class="{ 'border-primary-500 bg-primary-50 dark:bg-primary-900/20': address.is_default }"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <span class="font-semibold text-gray-900 dark:text-white">
                    {{ address.label }}
                  </span>
                  <span
                    v-if="address.is_default"
                    class="px-2 py-1 text-xs font-medium rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300"
                  >
                    {{ t('chef_address.default_badge') }}
                  </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ address.street }}
                </p>
                <p v-if="address.building_number || address.floor_number || address.apartment_number" class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                  <span v-if="address.building_number">{{ t('chef_address.building_number') }}: {{ address.building_number }}</span>
                  <span v-if="address.floor_number">, {{ t('chef_address.floor_number') }}: {{ address.floor_number }}</span>
                  <span v-if="address.apartment_number">, {{ t('chef_address.apartment_number') }}: {{ address.apartment_number }}</span>
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                  {{ [address.area?.name, address.district?.name, address.governorate?.name].filter(Boolean).join(', ') }}
                </p>
              </div>
              <div class="flex items-center gap-2">
                <!-- Set Default -->
                <button
                  v-if="!address.is_default"
                  @click="setDefault(address)"
                  class="p-2 text-gray-600 hover:text-primary-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :title="t('chef_address.set_as_default')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                  </svg>
                </button>
                <!-- Edit -->
                <Link
                  :href="route('chef.addresses.edit', address.id)"
                  class="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :title="t('common.edit')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <!-- Delete -->
                <button
                  @click="confirmDelete(address)"
                  class="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :title="t('common.delete')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ t('chef_address.no_addresses') }}</h3>
          <p class="mt-1 text-sm text-gray-500">{{ t('chef_address.no_addresses_description') }}</p>
        </div>
      </ComponentCard>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      v-model="showDeleteModal"
      :title="t('messages.areYouSure')"
      :message="t('chef_address.delete_confirmation')"
      :confirmText="t('common.delete')"
      :cancelText="t('common.cancel')"
      :loading="isDeleting"
      :loadingText="t('common.loading')"
      @confirm="deleteAddress"
    />
  </ChefLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link, usePage, router } from '@inertiajs/vue3'
import ChefLayout from '@/Components/layout/ChefLayout.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ComponentCard from '@/Components/common/ComponentCard.vue'
import ConfirmModal from '@/Components/modals/ConfirmModal.vue'

const { t } = useI18n()
const page = usePage()

const addresses = computed(() => page.props.addresses || [])

const showDeleteModal = ref(false)
const addressToDelete = ref(null)
const isDeleting = ref(false)

const confirmDelete = (address) => {
  addressToDelete.value = address
  showDeleteModal.value = true
}

const deleteAddress = () => {
  if (addressToDelete.value) {
    isDeleting.value = true
    router.delete(route('chef.addresses.destroy', addressToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        addressToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      },
    })
  }
}

const setDefault = (address) => {
  router.patch(route('chef.addresses.default', address.id))
}
</script>
