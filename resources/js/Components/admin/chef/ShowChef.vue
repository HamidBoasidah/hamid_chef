<template>
	<div class="space-y-6">
		<!-- Banner Image (moved to top) -->
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('chefs.banner') }}</h2>
			</div>
			<div class="p-4 sm:p-6">
				<div v-if="chef.banner" class="relative flex justify-center p-4">
					<img :src="`/storage/${chef.banner}`" alt="Chef Banner" class="max-h-64 rounded-lg border border-gray-200 object-contain dark:border-gray-800" />
				</div>
				<div v-else class="flex justify-center p-10">
					<p class="text-center text-sm text-gray-500 dark:text-gray-400">
						{{ t('chefs.noBanner') }}
					</p>
				</div>
			</div>
		</div>

		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800 flex items-center gap-4">
				<div class="h-14 w-14 overflow-hidden rounded-full border border-gray-200 dark:border-gray-800">
					<img v-if="chef.logo" :src="`/storage/${chef.logo}`" alt="" class="h-14 w-14 object-cover" />
					<ChefIcon v-else class="h-14 w-14 text-gray-400" />
				</div>

				<h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('chefs.chefInformation') }}</h2>
			</div>

			<div class="p-4 sm:p-6">
				<div class="grid grid-cols-1 gap-x-5 gap-y-6 md:grid-cols-2">
					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.name') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ chef.name ?? '—' }}</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.address') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ chef.address ?? '—' }}</p>
					</div>


					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.email') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ chef.email ?? '—' }}</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.phoneNumber') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ chef.phone ?? '—' }}</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chefs.baseHourlyRate') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ chef.base_hourly_rate ?? '—' }}</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chefs.rating') }}</label>
						<div class="flex items-center gap-2">
							<span class="text-lg font-semibold text-gray-900 dark:text-white">{{ chef.rating_avg || '0' }}</span>
							<div class="flex items-center">
								<StarIcon class="h-5 w-5 text-yellow-400" />
								<span class="text-sm text-gray-500 dark:text-gray-400 ml-1">متوسط التقييم</span>
							</div>
						</div>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('governorates.governorate') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ locale === 'ar' ? (chef.governorate?.name_ar ?? chef.governorate_name_ar ?? '—') : (chef.governorate?.name_en ?? chef.governorate_name_en ?? '—') }}</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('areas.area') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ locale === 'ar' ? (chef.area?.name_ar ?? chef.area_name_ar ?? '—') : (chef.area?.name_en ?? chef.area_name_en ?? '—') }}</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.status') }}</label>
						<span class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="chef.is_active ? 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'">{{ chef.is_active ? t('common.active') : t('common.inactive') }}</span>
					</div>

					<div class="md:col-span-2">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chefs.shortDescription') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ chef.short_description ?? '—' }}</p>
					</div>

					<div class="md:col-span-2">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chefs.longDescription') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ chef.long_description ?? '—' }}</p>
					</div>

					<div class="md:col-span-2">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('categories.categories') }}</label>
						<div v-if="chef.categories && chef.categories.length > 0" class="flex flex-wrap gap-2">
							<span
								v-for="category in chef.categories"
								:key="category.id"
								class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
								:class="category.is_active ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'"
							>
								{{ category.name }}
								<span v-if="!category.is_active" class="ml-1 text-xs opacity-60">({{ t('common.inactive') }})</span>
							</span>
						</div>
						<p v-else class="text-base text-gray-800 dark:text-white/90">{{ t('categories.noCategories') }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Gallery Section -->
		<ChefGalleryViewer 
			:images="chef.gallery || []"
			:loading="false"
			label="chefs.galleryImages"
		/>

		<div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
			<Link :href="route('admin.chefs.index')" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
				{{ t('buttons.backToList') }}
			</Link>

			<Link :href="route('admin.chefs.edit', chef.id)" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
				{{ t('buttons.edit') }}
			</Link>
		</div>
	</div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { ChefIcon, StarIcon } from '@/icons'
import ChefGalleryViewer from '@/Components/common/ChefGalleryViewer.vue'

const { t, locale } = useI18n()

defineProps({ chef: { type: Object, required: true } })
</script>

