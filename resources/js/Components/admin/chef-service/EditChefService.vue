<template>
	<div class="space-y-6">
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('chef_services.serviceInformation') }}</h2>
			</div>

			<div class="p-4 sm:p-6 dark:border-gray-800">
				<form @submit.prevent>
					<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.name') }}</label>
							<input v-model="form.name" type="text" :placeholder="t('chef_services.namePlaceholder')" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
							<p v-if="form.errors.name" class="mt-1 text-sm text-error-500">{{ form.errors.name }}</p>
						</div>

						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chefs.chefInformation') }}</label>
							<select v-model="form.chef_id" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<option :value="null">{{ t('common.select') }}</option>
								<option v-for="chef in chefs" :key="chef.id" :value="chef.id">{{ chef.name }}</option>
							</select>
							<p v-if="form.errors.chef_id" class="mt-1 text-sm text-error-500">{{ form.errors.chef_id }}</p>
						</div>

						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chef_services.serviceType') }}</label>
							<select v-model="form.service_type" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<option value="hourly">خدمة بالساعة</option>
								<option value="package">باقة</option>
							</select>
							<p v-if="form.errors.service_type" class="mt-1 text-sm text-error-500">{{ form.errors.service_type }}</p>
						</div>

						<div v-if="form.service_type === 'hourly'">
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chef_services.hourlyRate') }}</label>
							<input v-model="form.hourly_rate" type="number" step="0.01" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
							<p v-if="form.errors.hourly_rate" class="mt-1 text-sm text-error-500">{{ form.errors.hourly_rate }}</p>
						</div>

						<div v-if="form.service_type === 'hourly'">
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chef_services.minHours') }}</label>
							<input v-model="form.min_hours" type="number" min="1" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
							<p v-if="form.errors.min_hours" class="mt-1 text-sm text-error-500">{{ form.errors.min_hours }}</p>
						</div>

						<div v-if="form.service_type === 'package'">
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chef_services.packagePrice') }}</label>
							<input v-model="form.package_price" type="number" step="0.01" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
							<p v-if="form.errors.package_price" class="mt-1 text-sm text-error-500">{{ form.errors.package_price }}</p>
						</div>

						<div>
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chef_services.maxGuestsIncluded') }}</label>
							<input v-model="form.max_guests_included" type="number" min="1" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
							<p v-if="form.errors.max_guests_included" class="mt-1 text-sm text-error-500">{{ form.errors.max_guests_included }}</p>
						</div>

						<div v-if="form.allow_extra_guests">
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chef_services.extraGuestPrice') }}</label>
							<input v-model="form.extra_guest_price" type="number" step="0.01" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
							<p v-if="form.errors.extra_guest_price" class="mt-1 text-sm text-error-500">{{ form.errors.extra_guest_price }}</p>
						</div>

						<div class="md:col-span-2">
							<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.description') }}</label>
							<textarea v-model="form.description" rows="4" :placeholder="t('chef_services.descriptionPlaceholder')" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
							<p v-if="form.errors.description" class="mt-1 text-sm text-error-500">{{ form.errors.description }}</p>
						</div>

						<div class="md:col-span-2">
							<TagSelector 
								v-model="form.tags"
								:tags="tags"
								:error="form.errors.tags"
							/>
						</div>

					</div>
				</form>
			</div>
		</div>

		<!-- Feature Image Section -->
		<ImageUploadBox 
			v-model="form.feature_image" 
			input-id="feature-image-upload" 
			:initial-image="props.service.feature_image ? `/storage/${props.service.feature_image}` : null"
			label="chef_services.featureImage" 
		/>
		<p v-if="form.errors.feature_image" class="mt-1 text-sm text-error-500">{{ form.errors.feature_image }}</p>

		<!-- Service Images Section -->
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('chef_services.serviceImages') }}</h2>
			</div>
			<div class="p-4 sm:p-6">
				<GalleryManager 
					v-model:new-images="form.service_images"
					v-model:delete-ids="form.delete_service_image_ids"
					:existing-images="existingServiceImages"
					:max-images="8"
					label="chef_services.serviceImages"
				/>
				<p v-if="form.errors.service_images" class="mt-1 text-sm text-error-500">{{ form.errors.service_images }}</p>
				<p v-if="form.errors.delete_service_image_ids" class="mt-1 text-sm text-error-500">{{ form.errors.delete_service_image_ids }}</p>
			</div>
		</div>

		<!-- Status and Extra Guests Section -->
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h2 class="text-lg font-medium text-gray-800 dark:text-white">{{ t('chef_services.additionalSettings') }}</h2>
			</div>
			<div class="p-4 sm:p-6">
				<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
					<div class="md:col-span-2">
						<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('chef_services.allowExtraGuests') }}</label>
						<div class="mb-6 flex flex-wrap items-center gap-6 sm:gap-8">
							<div>
								<label for="toggle-extra-guests" class="flex cursor-pointer select-none items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-400">
									<div class="relative">
										<input type="checkbox" id="toggle-extra-guests" class="sr-only" v-model="form.allow_extra_guests" />
										<div class="block h-6 w-11 rounded-full" :class="form.allow_extra_guests ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'"></div>
										<div :class="form.allow_extra_guests ? 'translate-x-full' : 'translate-x-0'" class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-theme-sm duration-300 ease-linear"></div>
									</div>
									<span class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="{ 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500': form.allow_extra_guests, 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500': !form.allow_extra_guests }">{{ form.allow_extra_guests ? t('chef_services.allowExtraGuestsYes') : t('chef_services.allowExtraGuestsNo') }}</span>
								</label>
							</div>
						</div>
					</div>

					<div class="md:col-span-2">
						<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ t('common.status') }}</label>
						<div class="mb-6 flex flex-wrap items-center gap-6 sm:gap-8">
							<div>
								<label for="toggle-active" class="flex cursor-pointer select-none items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-400">
									<div class="relative">
										<input type="checkbox" id="toggle-active" class="sr-only" v-model="form.is_active" />
										<div class="block h-6 w-11 rounded-full" :class="form.is_active ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'"></div>
										<div :class="form.is_active ? 'translate-x-full' : 'translate-x-0'" class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-theme-sm duration-300 ease-linear"></div>
									</div>
									<span class="inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="{ 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500': form.is_active, 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500': !form.is_active }">{{ form.is_active ? t('common.active') : t('common.inactive') }}</span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
			<Link :href="route('admin.chef-services.index')" class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">{{ t('buttons.backToList') }}</Link>
			<button @click="update" :class="['bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition', form.processing ? 'cursor-not-allowed opacity-70' : '']">{{ form.processing ? t('common.loading') : t('buttons.update') }}</button>
		</div>
	</div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { computed, ref, watch } from 'vue'
import { useNotifications } from '@/composables/useNotifications'
import ImageUploadBox from '@/Components/common/ImageUploadBox.vue'
import GalleryManager from '@/Components/common/GalleryManager.vue'
import TagSelector from '@/Components/TagSelector.vue'

const { t } = useI18n()
const { success, error } = useNotifications()

const props = defineProps({ 
	service: { type: Object, required: true }, 
	chefs: { type: Array, required: true }, 
	tags: { type: Array, required: true }
})

const form = useForm({
	_method: 'PUT',
	chef_id: props.service.chef_id ?? null,
	name: props.service.name ?? '',
	description: props.service.description ?? '',
	service_type: props.service.service_type ?? 'hourly',
	hourly_rate: props.service.hourly_rate ?? 0,
	min_hours: props.service.min_hours ?? 1,
	package_price: props.service.package_price ?? 0,
	max_guests_included: props.service.max_guests_included ?? 1,
	allow_extra_guests: !!props.service.allow_extra_guests,
	extra_guest_price: props.service.extra_guest_price ?? 0,
	is_active: !!props.service.is_active,
	feature_image: props.service.feature_image ? `/storage/${props.service.feature_image}` : null,
	tags: props.service.tags ? props.service.tags.map(tag => tag.id) : [],
	service_images: [],
	delete_service_image_ids: [],
})

// Existing service images for display
const existingServiceImages = computed(() => {
	return props.service.images || []
})

function update() {
	// إنشاء نسخة من البيانات للتعديل
	const formData = { ...form.data() }
	
	// إذا كانت الصورة المميزة URL وليس ملف جديد، لا نرسلها
	if (typeof formData.feature_image === 'string' && formData.feature_image.startsWith('/storage/')) {
		delete formData.feature_image
	}
	
	form.transform(() => formData).post(route('admin.chef-services.update', props.service.id), {
		onSuccess: () => success(t('chef_services.serviceUpdatedSuccessfully')),
		onError: () => error(t('chef_services.serviceUpdateFailed')),
		preserveScroll: true,
	})
}
</script>