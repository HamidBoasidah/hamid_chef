<template>
  <aside
    :class="[
      'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 h-screen z-99999 bg-white dark:bg-gray-900 text-gray-900 border-gray-200 dark:border-gray-800',
      'lg:border-r rtl:lg:border-l rtl:lg:border-r-0',
      'w-[290px]',
      (isExpanded || isHovered) ? 'lg:w-[290px]' : 'lg:w-[90px]',
    ]"
    :style="sidebarInlineStyle"
    @mouseenter="!isExpanded && (isHovered = true)"
    @mouseleave="isHovered = false"
  >
    <div :class="['py-8 flex', (!isExpanded && !isHovered) ? 'lg:justify-center' : 'justify-start']">
      <Link href="/">
        <img
          v-if="isExpanded || isHovered || isMobileOpen"
          class="dark:hidden"
          src="/images/logo/logo.svg"
          alt="Logo"
          width="150"
          height="40"
        />
        <img
          v-if="isExpanded || isHovered || isMobileOpen"
          class="hidden dark:block"
          src="/images/logo/logo-dark.svg"
          alt="Logo"
          width="150"
          height="40"
        />
        <img
          v-else
          src="/images/logo/logo-icon.svg"
          alt="Logo"
          width="32"
          height="32"
        />
      </Link>
    </div>

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
      <nav class="mb-6">
        <div class="flex flex-col gap-4">
          <div>
            <h2
              :class="[
                'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
                !isExpanded && !isHovered
                  ? 'lg:justify-center'
                  : 'justify-start',
              ]"
            >
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ t('menu.menu') }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <li>
                <Link
                  :href="route('chefs.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive(route('chefs.index')),
                      'menu-item-inactive': !isActive(route('chefs.index')),
                    },
                  ]"
                >
                  <span :class="[isActive(route('chefs.index')) ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <ChefIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.chefs') }}
                  </span>
                </Link>
              </li>
              <li>
                <Link
                  :href="route('bookings.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive(route('bookings.index')),
                      'menu-item-inactive': !isActive(route('bookings.index')),
                    },
                  ]"
                >
                  <span :class="[isActive(route('bookings.index')) ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <CalenderIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.bookings') }}
                  </span>
                </Link>
              </li>
              <li>
                <Link
                  :href="route('chef-services.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive(route('chef-services.index')),
                      'menu-item-inactive': !isActive(route('chef-services.index')),
                    },
                  ]"
                >
                  <span :class="[isActive(route('chef-services.index')) ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <TaskIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.chef_services') }}
                  </span>
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link } from '@inertiajs/vue3'
import { ChefIcon, CalenderIcon, TaskIcon, HorizontalDots } from '../../icons'
import { useSidebar } from '@/composables/useSidebar'
import { route } from '@/route'

const { t } = useI18n()
const { isExpanded, isMobileOpen, isHovered, isMobile } = useSidebar()

const sidebarInlineStyle = computed(() => {
  const desktop = !isMobile.value
  return {
    insetInlineStart: desktop ? '0' : (isMobileOpen.value ? '0' : '-100%'),
    transition: 'inset-inline-start 300ms ease-in-out',
  }
})

const isActive = (path) => window?.location?.pathname === path
</script>
