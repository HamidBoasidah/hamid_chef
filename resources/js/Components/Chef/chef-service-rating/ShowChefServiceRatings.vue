<template>
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
      <h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('ratings.ratingsAndReviews') }}</h2>
    </div>
    
    <div class="p-4 sm:p-6">
      <div v-if="ratings && ratings.length > 0" class="space-y-4">
        <div 
          v-for="rating in ratings" 
          :key="rating.id"
          class="rounded-lg border border-gray-200 p-4 dark:border-gray-700"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <!-- Customer Info -->
              <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                  <UserCircleIcon class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-800 dark:text-white">
                    {{ rating.customer?.first_name }} {{ rating.customer?.last_name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDate(rating.created_at) }}
                  </p>
                </div>
              </div>

              <!-- Rating Stars -->
              <div class="flex items-center gap-1 mb-2">
                <StarIcon 
                  v-for="star in 5" 
                  :key="star"
                  class="h-4 w-4"
                  :class="star <= rating.rating ? 'text-yellow-400 fill-current' : 'text-gray-300 dark:text-gray-600'"
                />
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                  ({{ rating.rating }}/5)
                </span>
              </div>

              <!-- Review Text -->
              <p v-if="rating.review" class="text-sm text-gray-700 dark:text-gray-300">
                {{ rating.review }}
              </p>
              <p v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                {{ t('ratings.noReviewText') }}
              </p>

              <!-- Booking Info -->
              <div v-if="rating.booking" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ t('ratings.bookingDate') }}: {{ formatDate(rating.booking.date) }}
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
              <!-- Status Badge -->
              <Badge 
                :color="rating.is_active ? 'green' : 'red'"
                :text="rating.is_active ? t('common.active') : t('common.inactive')"
              />

              <!-- Action Buttons -->
              <div class="flex items-center gap-1">
                <button
                  @click="handleDeleteClick(rating.id)"
                  class="p-1 text-gray-400 hover:text-red-500 transition-colors"
                  :title="t('buttons.delete')"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-8">
        <StarIcon class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          {{ t('ratings.noRatings') }}
        </p>
      </div>
    </div>

    <!-- Delete Modal -->
    <DangerAlert 
      :isOpen="isDeleteModalOpen" 
      :title="t('messages.areYouSure')" 
      :message="t('ratings.confirmDeleteMessage')" 
      @close="closeDeleteModal" 
      @confirm="confirmDelete" 
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { router } from '@inertiajs/vue3'
import { route } from '@/route'
import { 
  UserCircleIcon, 
  StarIcon, 
  TrashIcon 
} from '@/icons'
import Badge from '@/Components/ui/Badge.vue'
import DangerAlert from '@/Components/modals/DangerAlert.vue'

const { t } = useI18n()

defineProps({
  ratings: {
    type: Array,
    default: () => []
  }
})

// Delete modal
const isDeleteModalOpen = ref(false)
const ratingToDeleteId = ref(null)

const formatDate = (dateString) => {
  if (!dateString) return '—'
  return new Date(dateString).toLocaleDateString('ar-SA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const handleDeleteClick = (ratingId) => {
  ratingToDeleteId.value = ratingId
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  ratingToDeleteId.value = null
}

const confirmDelete = () => {
  if (ratingToDeleteId.value) {
    router.delete(route('admin.chef-service-ratings.destroy', ratingToDeleteId.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeDeleteModal()
        // Refresh the page to show updated data
        router.reload({ only: ['service'] })
      },
      onError: () => {
        closeDeleteModal()
      }
    })
  }
}
</script>