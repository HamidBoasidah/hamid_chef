<template>
	<div class="space-y-6">
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800 flex items-center gap-4">
				<div class="h-14 w-14 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 flex items-center justify-center bg-brand-50 dark:bg-brand-500/15">
					<img v-if="service.feature_image" :src="`/storage/${service.feature_image}`" :alt="service.name" class="h-14 w-14 object-cover rounded-2xl" />
					<TaskIcon v-else class="h-8 w-8 text-brand-600 dark:text-brand-200" />
				</div>

				<div>
					<h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('chef_services.serviceInformation') }}</h2>
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ service.name }}</p>
				</div>
			</div>

			<div class="p-4 sm:p-6">
				<div class="grid grid-cols-1 gap-x-5 gap-y-6 md:grid-cols-2">
					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.name') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ service.name ?? '—' }}</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chefs.chefInformation') }}</label>
						<div v-if="service.chef" class="flex items-center gap-3">
							<div class="h-10 w-10 overflow-hidden rounded-full border border-gray-200 dark:border-gray-800">
								<img v-if="service.chef.logo" :src="`/storage/${service.chef.logo}`" :alt="service.chef.name" class="h-10 w-10 object-cover" />
								<div v-else class="h-10 w-10 flex items-center justify-center bg-gray-100 dark:bg-gray-700">
									<ChefIcon class="h-6 w-6 text-gray-400" />
								</div>
							</div>
							<div>
								<p class="text-base text-gray-800 dark:text-white/90 font-medium">{{ service.chef.name }}</p>
								<p class="text-sm text-gray-500 dark:text-gray-400">{{ service.chef.email }}</p>
							</div>
						</div>
						<p v-else class="text-base text-gray-800 dark:text-white/90">—</p>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_services.serviceType') }}</label>
						<span v-if="service.service_type" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="service.service_type === 'hourly' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'">
							{{ service.service_type === 'hourly' ? 'خدمة بالساعة' : 'باقة' }}
						</span>
						<p v-else class="text-base text-gray-800 dark:text-white/90">—</p>
					</div>

					<div v-if="service.service_type === 'hourly'">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_services.hourlyRate') }}</label>
						<div class="flex items-center gap-2">
							<span class="text-2xl font-bold text-gray-900 dark:text-white">{{ service.hourly_rate || '0' }}</span>
							<span class="text-sm text-gray-500 dark:text-gray-400">ريال/ساعة</span>
						</div>
					</div>

					<div v-if="service.service_type === 'hourly'">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_services.minHours') }}</label>
						<div class="flex items-center gap-2">
							<span class="text-lg font-semibold text-gray-900 dark:text-white">{{ service.min_hours || '0' }}</span>
							<span class="text-sm text-gray-500 dark:text-gray-400">ساعة كحد أدنى</span>
						</div>
					</div>

					<div v-if="service.service_type === 'package'">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_services.packagePrice') }}</label>
						<div class="flex items-center gap-2">
							<span class="text-2xl font-bold text-gray-900 dark:text-white">{{ service.package_price || '0' }}</span>
							<span class="text-sm text-gray-500 dark:text-gray-400">ريال</span>
						</div>
					</div>

					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_services.maxGuestsIncluded') }}</label>
						<div class="flex items-center gap-2">
							<span class="text-lg font-semibold text-gray-900 dark:text-white">{{ service.max_guests_included || '0' }}</span>
							<span class="text-sm text-gray-500 dark:text-gray-400">ضيف مشمول</span>
						</div>
					</div>



					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_services.allowExtraGuests') }}</label>
						<span class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="service.allow_extra_guests ? 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'">{{ service.allow_extra_guests ? t('chef_services.allowExtraGuestsYes') : t('chef_services.allowExtraGuestsNo') }}</span>
					</div>

					<div v-if="service.allow_extra_guests">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('chef_services.extraGuestPrice') }}</label>
						<div class="flex items-center gap-2">
							<span class="text-lg font-semibold text-gray-900 dark:text-white">{{ service.extra_guest_price || '0' }}</span>
							<span class="text-sm text-gray-500 dark:text-gray-400">ريال/ضيف إضافي</span>
						</div>
					</div>



					<div>
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.status') }}</label>
						<span class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="service.is_active ? 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'">{{ service.is_active ? t('common.active') : t('common.inactive') }}</span>
					</div>

					<div class="md:col-span-2">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('common.description') }}</label>
						<p class="text-base text-gray-800 dark:text-white/90">{{ service.description ?? '—' }}</p>
					</div>

					<div class="md:col-span-2">
						<label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('menu.tags') }}</label>
						<div v-if="service.tags && service.tags.length > 0" class="flex flex-wrap gap-2">
							<span
								v-for="tag in service.tags"
								:key="tag.id"
								class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
								:class="tag.is_active ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'"
							>
								{{ tag.name }}
								<span v-if="!tag.is_active" class="ml-1 text-xs opacity-60">({{ t('common.inactive') }})</span>
							</span>
						</div>
						<p v-else class="text-base text-gray-800 dark:text-white/90">{{ t('tags.noTag') }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Service Images Section -->
		<ChefGalleryViewer 
			:images="service.images || []"
			:loading="false"
			label="chef_services.serviceImages"
			empty-message="لا توجد صور للخدمة"
		/>

		<!-- Equipment Section -->
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ $t('equipment.equipment_management') }}</h2>
			</div>
			<div class="p-4 sm:p-6">
				<div v-if="service.equipment && service.equipment.length > 0" class="space-y-4">
					<div
						v-for="equipment in service.equipment"
						:key="equipment.id"
						class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
					>
						<div class="flex items-start justify-between">
							<div class="flex-1">
								<div class="flex items-center gap-3 mb-2">
									<h3 class="text-base font-medium text-gray-900 dark:text-white">{{ equipment.name }}</h3>
									<span
										class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
										:class="equipment.is_included 
											? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' 
											: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'"
									>
										{{ equipment.is_included ? $t('equipment.included') : $t('equipment.client_provided') }}
									</span>
									<!-- equipment.is_active removed: field no longer exists in the equipment table -->
								</div>
								<p v-if="equipment.description" class="text-sm text-gray-600 dark:text-gray-400">
									{{ equipment.description }}
								</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
					<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
					</svg>
					<p class="mt-2">{{ $t('equipment.no_equipment') }}</p>
				</div>
			</div>
		</div>

		<!-- Service Ratings Section -->
		<ShowChefServiceRatings :ratings="service.ratings || []" />

		<div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
			<Link :href="route('chef-services.index')" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
				{{ t('buttons.backToList') }}
			</Link>

			<Link :href="route('chef-services.edit', service.id)" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
				{{ t('buttons.edit') }}
			</Link>
		</div>
	</div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { TaskIcon, ChefIcon } from '@/icons'
import ChefGalleryViewer from '@/Components/common/ChefGalleryViewer.vue'
import ShowChefServiceRatings from '@/Components/Chef/chef-service-rating/ShowChefServiceRatings.vue'

const { t, locale } = useI18n()

defineProps({ service: { type: Object, required: true } })
</script>