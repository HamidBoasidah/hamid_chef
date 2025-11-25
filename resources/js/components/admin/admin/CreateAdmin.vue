<template>
  <div class="space-y-6">
    <div class="mx-auto max-w-3xl">
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ t('common.name') }}</label>
            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">{{ t('common.email') }}</label>
            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">{{ t('common.phoneNumber') }}</label>
            <input v-model="form.phone_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            <p v-if="form.errors.phone_number" class="text-sm text-red-600">{{ form.errors.phone_number }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">{{ t('common.password') }}</label>
            <div class="relative">
              <input :type="showPassword ? 'text' : 'password'" v-model="form.password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm pr-10" />
              <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                <svg v-if="!showPassword" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10 4.5C6.1 4.5 3 7 1.7 9.5 3 12 6.1 14.5 10 14.5s7-2.5 8.3-5C17 7 13.9 4.5 10 4.5zM10 12a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" />
                </svg>
                <svg v-else class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.3 3.3L16.7 16.7M7.5 7.5a2.5 2.5 0 103.5 3.5" />
                </svg>
              </button>
            </div>
            <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">{{ t('common.confirmPassword') }}</label>
            <div class="relative">
              <input :type="showPassword ? 'text' : 'password'" v-model="form.password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm pr-10" />
              <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                <svg v-if="!showPassword" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10 4.5C6.1 4.5 3 7 1.7 9.5 3 12 6.1 14.5 10 14.5s7-2.5 8.3-5C17 7 13.9 4.5 10 4.5zM10 12a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" />
                </svg>
                <svg v-else class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.3 3.3L16.7 16.7M7.5 7.5a2.5 2.5 0 103.5 3.5" />
                </svg>
              </button>
            </div>
            <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">{{ form.errors.password_confirmation }}</p>
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">{{ t('common.role') }}</label>
            <select v-model="form.role_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
              <option value="">-- {{ t('common.select') }} --</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
            <p v-if="form.errors.role_id" class="text-sm text-red-600">{{ form.errors.role_id }}</p>
          </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
          <button type="submit" :disabled="form.processing" class="inline-flex items-center rounded bg-brand-500 px-4 py-2 text-white">
            {{ t('common.save') }}
          </button>
          <inertia-link :href="route('admin.admins.index')" class="text-sm text-gray-600 hover:underline">{{ t('common.cancel') }}</inertia-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { computed, ref } from 'vue'
import { route } from '@/route'

const props = defineProps({ roles: Array })
const roles = computed(() => props.roles || [])

const { t } = useI18n()

const form = useForm({
  name: '',
  email: '',
  phone_number: '',
  password: '',
  password_confirmation: '',
  role_id: '',
})

const showPassword = ref(false)

function submit() {
  form.post(route('admin.admins.store'), {
    onSuccess: () => {
      // optionally redirect handled by server
    },
  })
}
</script>
