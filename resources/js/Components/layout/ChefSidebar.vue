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
      <Link :href="route('chef.dashboard')">
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
          <!-- Main Menu -->
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
              <!-- Dashboard -->
              <li>
                <Link
                  :href="route('chef.dashboard')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.dashboard') || isActive('chef.index'),
                      'menu-item-inactive': !isActive('chef.dashboard') && !isActive('chef.index'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.dashboard') || isActive('chef.index') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <GridIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.dashboard') }}
                  </span>
                </Link>
              </li>

              <!-- Bookings -->
              <li>
                <Link
                  :href="route('chef.bookings.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.bookings'),
                      'menu-item-inactive': !isActive('chef.bookings'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.bookings') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <CalenderIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.bookings') }}
                  </span>
                </Link>
              </li>

              <!-- Services -->
              <li>
                <Link
                  :href="route('chef.services.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.services'),
                      'menu-item-inactive': !isActive('chef.services'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.services') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <TaskIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.my_services') }}
                  </span>
                </Link>
              </li>
            </ul>
          </div>

          <!-- Schedule Section -->
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
                {{ t('menu.schedule') }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <!-- Working Hours -->
              <li>
                <Link
                  :href="route('chef.working-hours.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.working-hours'),
                      'menu-item-inactive': !isActive('chef.working-hours'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.working-hours') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <ClockIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.working_hours') }}
                  </span>
                </Link>
              </li>

              <!-- Vacations -->
              <li>
                <Link
                  :href="route('chef.vacations.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.vacations'),
                      'menu-item-inactive': !isActive('chef.vacations'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.vacations') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <CalenderIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.vacations') }}
                  </span>
                </Link>
              </li>
            </ul>
          </div>

          <!-- Account Section -->
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
                {{ t('menu.account') }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <!-- Reports Dropdown -->
              <li>
                <button
                  @click="toggleReportsSubmenu"
                  :class="[
                    'menu-item group w-full',
                    {
                      'menu-item-active': isReportsSubmenuOpen,
                      'menu-item-inactive': !isReportsSubmenuOpen,
                    },
                    !isExpanded && !isHovered
                      ? 'lg:justify-center'
                      : 'lg:justify-start',
                  ]"
                >
                  <span
                    :class="[
                      isReportsSubmenuOpen
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]"
                  >
                    <BarChartIcon />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="menu-item-text"
                  >
                    {{ t('menu.reports') }}
                  </span>
                  <ChevronDownIcon
                    v-if="isExpanded || isHovered || isMobileOpen"
                    :class="[
                      'ml-auto w-5 h-5 transition-transform duration-200',
                      {
                        'rotate-180 text-brand-500': isReportsSubmenuOpen,
                      },
                    ]"
                  />
                </button>
                <transition
                  @enter="startTransition"
                  @after-enter="endTransition"
                  @before-leave="startTransition"
                  @after-leave="endTransition"
                >
                  <div
                    v-show="
                      isReportsSubmenuOpen &&
                      (isExpanded || isHovered || isMobileOpen)
                    "
                  >
                    <ul class="mt-2 space-y-1 ml-9">
                      <li>
                        <Link
                          :href="route('chef.reports.bookings')"
                          :class="[
                            'menu-dropdown-item',
                            {
                              'menu-dropdown-item-active': isActive('chef.reports.bookings'),
                              'menu-dropdown-item-inactive': !isActive('chef.reports.bookings'),
                            },
                          ]"
                        >
                          {{ t('reports.bookings_report') }}
                        </Link>
                      </li>
                      <li>
                        <Link
                          :href="route('chef.reports.earnings')"
                          :class="[
                            'menu-dropdown-item',
                            {
                              'menu-dropdown-item-active': isActive('chef.reports.earnings'),
                              'menu-dropdown-item-inactive': !isActive('chef.reports.earnings'),
                            },
                          ]"
                        >
                          {{ t('reports.earnings_report') }}
                        </Link>
                      </li>
                      <li>
                        <Link
                          :href="route('chef.reports.services')"
                          :class="[
                            'menu-dropdown-item',
                            {
                              'menu-dropdown-item-active': isActive('chef.reports.services'),
                              'menu-dropdown-item-inactive': !isActive('chef.reports.services'),
                            },
                          ]"
                        >
                          {{ t('reports.services_report') }}
                        </Link>
                      </li>
                    </ul>
                  </div>
                </transition>
              </li>

              <!-- Wallet -->
              <li>
                <Link
                  :href="route('chef.wallet.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.wallet'),
                      'menu-item-inactive': !isActive('chef.wallet'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.wallet') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <WalletIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.wallet') }}
                  </span>
                </Link>
              </li>

              <!-- Profile -->
              <li>
                <Link
                  :href="route('chef.profile.edit')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.profile'),
                      'menu-item-inactive': !isActive('chef.profile'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.profile') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <UserCircleIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.profile') }}
                  </span>
                </Link>
              </li>

              <!-- KYC -->
              <li>
                <Link
                  :href="route('chef.kyc.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.kyc'),
                      'menu-item-inactive': !isActive('chef.kyc'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.kyc') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <DocumentIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.kyc') }}
                  </span>
                </Link>
              </li>

              <!-- Addresses -->
              <li>
                <Link
                  :href="route('chef.addresses.index')"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive('chef.addresses'),
                      'menu-item-inactive': !isActive('chef.addresses'),
                    },
                  ]"
                >
                  <span :class="[isActive('chef.addresses') ? 'menu-item-icon-active' : 'menu-item-icon-inactive']">
                    <MapPinIcon />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                    {{ t('menu.addresses') }}
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
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link, usePage } from '@inertiajs/vue3'
import {
  GridIcon,
  UserCircleIcon,
  CalenderIcon,
  TaskIcon,
  HorizontalDots,
  ClockIcon,
  WalletIcon,
  BarChartIcon,
  ChevronDownIcon,
} from '../../icons'
import DocumentIcon from '../../icons/DocumentIcon.vue'
import MapPinIcon from '../../icons/MapPinIcon.vue'
import { useSidebar } from '@/composables/useSidebar'

const { t } = useI18n()
const page = usePage()
const { isExpanded, isMobileOpen, isHovered, isMobile } = useSidebar()

const isReportsSubmenuOpen = ref(false)

const sidebarInlineStyle = computed(() => {
  const desktop = !isMobile.value
  return {
    insetInlineStart: desktop ? '0' : (isMobileOpen.value ? '0' : '-100%'),
    transition: 'inset-inline-start 300ms ease-in-out',
  }
})

// Check if route is active by route name pattern
const isActive = (routePattern) => {
  const currentRoute = page.props.ziggy?.location || window?.location?.pathname
  const routeName = route().current()

  if (routeName) {
    return routeName.startsWith(routePattern)
  }

  return false
}

// Toggle reports submenu
const toggleReportsSubmenu = () => {
  isReportsSubmenuOpen.value = !isReportsSubmenuOpen.value
}

// Transition handlers
const startTransition = (el) => {
  el.style.height = '0'
}

const endTransition = (el) => {
  el.style.height = ''
}

// Auto-open reports submenu if on a reports page
if (isActive('chef.reports')) {
  isReportsSubmenuOpen.value = true
}
</script>
