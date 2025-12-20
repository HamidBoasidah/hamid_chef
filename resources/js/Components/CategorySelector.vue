<template>
  <div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
      {{ $t('categories.categories') }}
    </label>
    
    <div class="relative" ref="multiSelectRef">
      <div
        @click="toggleDropdown"
        class="dark:bg-dark-900 h-11 flex items-center w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        :class="{ 
          'text-gray-800 dark:text-white/90': isOpen,
          'cursor-not-allowed opacity-50': disabled 
        }"
      >
        <span v-if="selectedCategories.length === 0" class="text-gray-400 dark:text-gray-500">
          {{ $t('categories.selectCategories') }}
        </span>
        
        <!-- Show count when many categories are selected -->
        <span v-else-if="selectedCategories.length > 3" class="text-gray-600 dark:text-gray-300 text-sm">
          {{ selectedCategories.length }} {{ $t('categories.categoriesSelected') }}
        </span>
        
        <div v-if="selectedCategories.length <= 3" class="flex flex-wrap items-center flex-auto gap-2">
          <div
            v-for="category in selectedCategories"
            :key="category.id"
            class="group flex items-center justify-center h-[30px] rounded-full border-[0.7px] border-transparent bg-gray-100 py-1 pl-2.5 pr-2 text-sm text-gray-800 hover:border-gray-200 dark:bg-gray-800 dark:text-white/90 dark:hover:border-gray-800"
          >
            <span>{{ category.name }}</span>
            <button
              @click.stop="removeCategory(category)"
              class="pl-2 text-gray-500 cursor-pointer group-hover:text-gray-400 dark:text-gray-400"
              aria-label="Remove category"
            >
              <svg
                role="button"
                width="14"
                height="14"
                viewBox="0 0 14 14"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M3.40717 4.46881C3.11428 4.17591 3.11428 3.70104 3.40717 3.40815C3.70006 3.11525 4.17494 3.11525 4.46783 3.40815L6.99943 5.93975L9.53095 3.40822C9.82385 3.11533 10.2987 3.11533 10.5916 3.40822C10.8845 3.70112 10.8845 4.17599 10.5916 4.46888L8.06009 7.00041L10.5916 9.53193C10.8845 9.82482 10.8845 10.2997 10.5916 10.5926C10.2987 10.8855 9.82385 10.8855 9.53095 10.5926L6.99943 8.06107L4.46783 10.5927C4.17494 10.8856 3.70006 10.8856 3.40717 10.5927C3.11428 10.2998 3.11428 9.8249 3.40717 9.53201L5.93877 7.00041L3.40717 4.46881Z"
                  fill="currentColor"
                />
              </svg>
            </button>
          </div>
        </div>
        
        <svg
          class="ml-auto"
          :class="{ 'transform rotate-180': isOpen }"
          width="20"
          height="20"
          viewBox="0 0 20 20"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M4.79175 7.39551L10.0001 12.6038L15.2084 7.39551"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
      </div>
      
      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div
          v-if="isOpen && !disabled"
          class="absolute z-50 w-full bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-900 dark:border-gray-700"
          :class="{
            'mt-1': dropdownPosition === 'bottom',
            'mb-1 bottom-full': dropdownPosition === 'top'
          }"
        >
          <!-- Search Input -->
          <div class="p-3 border-b border-gray-200 dark:border-gray-700">
            <div class="relative">
              <svg
                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="$t('categories.searchCategories')"
                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:border-brand-300 focus:ring-1 focus:ring-brand-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                @click.stop
                @keydown="handleKeydown"
              />
            </div>
          </div>
          
          <!-- Scroll indicators -->
          <div class="relative">
            <!-- Top scroll indicator -->
            <div 
              v-if="showScrollTop" 
              class="absolute top-0 left-0 right-0 h-4 bg-gradient-to-b from-white to-transparent dark:from-gray-900 z-10 pointer-events-none"
            ></div>
            
            <ul
              ref="scrollContainer"
              @scroll="handleScroll"
              class="overflow-y-scroll divide-y divide-gray-200 custom-scrollbar dark:divide-gray-800"
              :style="{ 
                maxHeight: maxDropdownHeight + 'px',
                minHeight: '150px'
              }"
              role="listbox"
              aria-multiselectable="true"
            >
            <li
              v-for="category in availableCategories"
              :key="category.id"
              @click="toggleCategory(category)"
              class="relative flex items-center w-full px-3 py-2 border-transparent cursor-pointer first:rounded-t-lg last:rounded-b-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800"
              :class="{ 'bg-gray-50 dark:bg-white/[0.03]': isSelected(category) }"
              role="option"
              :aria-selected="isSelected(category)"
            >
              <span class="grow">{{ category.name }}</span>
              <span v-if="category.slug" class="text-xs text-gray-500 dark:text-gray-400 mr-2">
                {{ category.slug }}
              </span>
              <svg
                v-if="isSelected(category)"
                class="w-5 h-5 text-brand-500 dark:text-brand-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                ></path>
              </svg>
            </li>
            <li v-if="availableCategories.length === 0" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
              <div v-if="searchQuery">
                {{ $t('categories.noSearchResults') }}
              </div>
              <div v-else>
                {{ $t('categories.noCategories') }}
              </div>
            </li>
            </ul>
            
            <!-- Bottom scroll indicator -->
            <div 
              v-if="showScrollBottom" 
              class="absolute bottom-0 left-0 right-0 h-4 bg-gradient-to-t from-white to-transparent dark:from-gray-900 z-10 pointer-events-none"
            ></div>
          </div>
          


          <!-- Footer with statistics -->
          <div v-if="props.categories.length > 0" class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-lg">
            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
              <span>
                {{ selectedCategories.length }} {{ $t('categories.selected') }} / {{ availableCategories.length }} {{ $t('categories.showing') }}
                <span v-if="searchQuery && availableCategories.length !== props.categories.length">
                  ({{ props.categories.length }} {{ $t('categories.total') }})
                </span>
              </span>
              <button
                v-if="selectedCategories.length > 0"
                @click.stop="clearAll"
                class="text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300"
              >
                {{ $t('categories.clearAll') }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>
    
    <!-- Error Message -->
    <div v-if="error" class="text-sm text-red-600 dark:text-red-400">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  modelValue: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const selectedCategories = ref([])
const multiSelectRef = ref(null)
const searchQuery = ref('')
const scrollContainer = ref(null)
const showScrollTop = ref(false)
const showScrollBottom = ref(false)
const dropdownPosition = ref('bottom')
const maxDropdownHeight = ref(240) // Default max height

// Available categories (filtered by search)
const availableCategories = computed(() => {
  const categories = props.categories || []
  if (!searchQuery.value) {
    return categories
  }
  
  const query = searchQuery.value.toLowerCase()
  return categories.filter(category => 
    category.name.toLowerCase().includes(query) ||
    (category.slug && category.slug.toLowerCase().includes(query))
  )
})

// Initialize selected categories from modelValue
watch(() => props.modelValue, (newValue) => {
  if (Array.isArray(newValue)) {
    selectedCategories.value = props.categories.filter(category => 
      newValue.includes(category.id)
    )
  }
}, { immediate: true })

// Watch categories prop changes
watch(() => props.categories, (newCategories) => {
  if (Array.isArray(props.modelValue) && newCategories) {
    selectedCategories.value = newCategories.filter(category => 
      props.modelValue.includes(category.id)
    )
  }
}, { immediate: true })

// Watch search query changes to update scroll indicators
watch(searchQuery, () => {
  setTimeout(checkScrollIndicators, 100)
})

// Watch available categories changes to update scroll indicators
watch(availableCategories, () => {
  setTimeout(checkScrollIndicators, 100)
})

const toggleDropdown = () => {
  if (!props.disabled) {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
      searchQuery.value = ''
      // Check available space and position dropdown accordingly
      setTimeout(() => {
        checkDropdownPosition()
        checkScrollIndicators()
        // Force scroll container to recalculate
        if (scrollContainer.value) {
          scrollContainer.value.scrollTop = 0
        }
      }, 100) // Increased timeout for better DOM update
    }
  }
}

const checkDropdownPosition = () => {
  if (!multiSelectRef.value) return
  
  const rect = multiSelectRef.value.getBoundingClientRect()
  const viewportHeight = window.innerHeight
  const spaceBelow = viewportHeight - rect.bottom - 20 // 20px margin
  const spaceAbove = rect.top - 20 // 20px margin
  
  // Calculate max height based on available space
  const minHeight = 200 // Increased minimum height for better usability
  const maxHeight = 400 // Increased maximum height
  
  // Account for search input (60px) and footer (40px) = 100px total
  const reservedSpace = 100
  
  if (spaceAbove > spaceBelow && spaceBelow < 300) {
    dropdownPosition.value = 'top'
    maxDropdownHeight.value = Math.min(Math.max(spaceAbove - reservedSpace, minHeight), maxHeight)
  } else {
    dropdownPosition.value = 'bottom'
    maxDropdownHeight.value = Math.min(Math.max(spaceBelow - reservedSpace, minHeight), maxHeight)
  }
  
  // Ensure minimum scrollable height
  if (maxDropdownHeight.value < minHeight) {
    maxDropdownHeight.value = minHeight
  }
}

const toggleCategory = (category) => {
  const index = selectedCategories.value.findIndex(selected => selected.id === category.id)
  
  if (index === -1) {
    selectedCategories.value.push(category)
  } else {
    selectedCategories.value.splice(index, 1)
  }
  
  updateValue()
}

const removeCategory = (category) => {
  const index = selectedCategories.value.findIndex(selected => selected.id === category.id)
  
  if (index !== -1) {
    selectedCategories.value.splice(index, 1)
    updateValue()
  }
}

const isSelected = (category) => {
  return selectedCategories.value.some(selected => selected.id === category.id)
}

const updateValue = () => {
  const categoryIds = selectedCategories.value.map(category => category.id)
  emit('update:modelValue', categoryIds)
}

const clearAll = () => {
  selectedCategories.value = []
  updateValue()
}

const handleScroll = () => {
  if (!scrollContainer.value) return
  
  const { scrollTop, scrollHeight, clientHeight } = scrollContainer.value
  
  // Show top indicator if scrolled down
  showScrollTop.value = scrollTop > 10
  
  // Show bottom indicator if not at bottom
  showScrollBottom.value = scrollTop < scrollHeight - clientHeight - 10
}

const checkScrollIndicators = () => {
  if (!scrollContainer.value) return
  
  // Force reflow to ensure accurate measurements
  scrollContainer.value.offsetHeight
  
  const { scrollHeight, clientHeight, scrollTop } = scrollContainer.value
  
  // Show indicators based on scroll position and content overflow
  showScrollTop.value = scrollTop > 10
  showScrollBottom.value = scrollHeight > clientHeight && scrollTop < scrollHeight - clientHeight - 10
  
  // If content overflows but we're at the top, show bottom indicator
  if (scrollTop === 0 && scrollHeight > clientHeight) {
    showScrollBottom.value = true
  }
}



const handleKeydown = (event) => {
  if (!scrollContainer.value) return
  
  // Handle keyboard navigation for scrolling
  if (event.key === 'ArrowDown' && event.ctrlKey) {
    event.preventDefault()
    scrollContainer.value.scrollTo({ 
      top: scrollContainer.value.scrollHeight, 
      behavior: 'smooth' 
    })
  } else if (event.key === 'ArrowUp' && event.ctrlKey) {
    event.preventDefault()
    scrollContainer.value.scrollTo({ top: 0, behavior: 'smooth' })
  } else if (event.key === 'PageDown') {
    event.preventDefault()
    scrollContainer.value.scrollBy({ top: 200, behavior: 'smooth' })
  } else if (event.key === 'PageUp') {
    event.preventDefault()
    scrollContainer.value.scrollBy({ top: -200, behavior: 'smooth' })
  }
}

const handleClickOutside = (event) => {
  if (multiSelectRef.value && !multiSelectRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

const handleResize = () => {
  if (isOpen.value) {
    checkDropdownPosition()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('resize', handleResize)
  window.addEventListener('scroll', handleResize)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('scroll', handleResize)
})
</script>

<style scoped>
/* Enhanced scrollbar styles */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.7) rgba(243, 244, 246, 0.3);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 10px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(243, 244, 246, 0.8);
  border-radius: 5px;
  margin: 2px 0;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.8);
  border-radius: 5px;
  border: 1px solid rgba(243, 244, 246, 0.8);
  min-height: 20px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(107, 114, 128, 0.9);
}

.custom-scrollbar::-webkit-scrollbar-thumb:active {
  background-color: rgba(75, 85, 99, 1);
}

/* Dark mode scrollbar */
.dark .custom-scrollbar {
  scrollbar-color: rgba(107, 114, 128, 0.7) rgba(55, 65, 81, 0.3);
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(55, 65, 81, 0.8);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(107, 114, 128, 0.8);
  border: 1px solid rgba(55, 65, 81, 0.8);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.9);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:active {
  background-color: rgba(209, 213, 219, 1);
}

/* Smooth scrolling */
.custom-scrollbar {
  scroll-behavior: smooth;
}

/* Add padding to prevent content from hiding behind scrollbar */
.custom-scrollbar li {
  padding-right: 16px;
  margin-right: 2px;
}

/* Ensure scrollbar is always visible when content overflows */
.custom-scrollbar {
  overflow-y: scroll !important;
}

/* Force minimum content height to enable scrolling */
.custom-scrollbar:empty::before {
  content: '';
  display: block;
  height: 1px;
}
</style>