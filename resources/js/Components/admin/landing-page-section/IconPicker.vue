<template>
  <div>
    <!-- Icon Picker Button -->
    <button
      @click="openPicker"
      type="button"
      class="w-full h-12 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 text-sm text-gray-800 dark:text-white hover:border-primary focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all flex items-center justify-between"
    >
      <span class="flex items-center gap-2">
        <component :is="getIconComponent(modelValue)" :size="20" v-if="modelValue && getIconComponent(modelValue)" />
        <span>{{ modelValue || placeholder }}</span>
      </span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Icon Picker Modal -->
    <Teleport to="body">
      <div v-if="showPicker" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closePicker">
        <div class="flex items-center justify-center min-h-screen px-4">
          <div class="fixed inset-0 bg-black opacity-50"></div>
          <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 z-10">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ title }}
                </h3>
                <button @click="closePicker" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <!-- Search Input -->
              <input 
                v-model="searchQuery" 
                type="text" 
                :placeholder="searchPlaceholder"
                class="w-full h-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 text-sm text-gray-800 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
              />
            </div>
            
            <!-- Icons Grid -->
            <div class="p-4 overflow-y-auto max-h-[60vh]">
              <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2">
                <button
                  v-for="icon in filteredIcons"
                  :key="icon"
                  @click="selectIcon(icon)"
                  class="flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all hover:border-primary hover:bg-primary/5"
                  :class="selectedIcon === icon ? 'border-primary bg-primary/10' : 'border-gray-200 dark:border-gray-700'"
                  :title="icon"
                >
                  <component :is="getIconComponent(icon)" :size="24" v-if="getIconComponent(icon)" />
                  <span class="text-xs mt-1 truncate w-full text-center text-gray-700 dark:text-white">{{ icon }}</span>
                </button>
              </div>
              <div v-if="filteredIcons.length === 0" class="text-center py-12 text-gray-500 dark:text-white">
                {{ noIconsText }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import * as lucideIcons from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Select Icon'
  },
  title: {
    type: String,
    default: 'Select Icon'
  },
  searchPlaceholder: {
    type: String,
    default: 'Search icons...'
  },
  noIconsText: {
    type: String,
    default: 'No icons found'
  }
})

const emit = defineEmits(['update:modelValue'])

const showPicker = ref(false)
const searchQuery = ref('')
const selectedIcon = ref(props.modelValue)

// قائمة كاملة من أيقونات Lucide
const availableIcons = [
  'Certificate', 'ShieldCheck', 'Clock', 'DollarSign', 'Users', 'Heart',
  'Star', 'Award', 'CheckCircle', 'ThumbsUp', 'Smile', 'Zap',
  'TrendingUp', 'Target', 'Gift', 'Crown', 'Medal', 'Trophy',
  'Briefcase', 'Calendar', 'Phone', 'Mail', 'MapPin', 'Home',
  'ShoppingCart', 'CreditCard', 'Package', 'Truck', 'Box', 'Archive',
  'FileText', 'Clipboard', 'Book', 'Bookmark', 'Folder', 'Save',
  'Download', 'Upload', 'Share', 'Link', 'ExternalLink', 'Paperclip',
  'Image', 'Camera', 'Video', 'Music', 'Headphones', 'Mic',
  'Settings', 'Tool', 'Wrench', 'Sliders', 'Filter', 'Search',
  'Bell', 'AlertCircle', 'AlertTriangle', 'Info', 'HelpCircle', 'MessageCircle',
  'Lock', 'Unlock', 'Key', 'Eye', 'EyeOff', 'User',
  'UserPlus', 'UserCheck', 'UserX', 'Users', 'UserCircle', 'Shield',
  'Globe', 'Wifi', 'Bluetooth', 'Battery', 'Power', 'Cpu',
  'Server', 'Database', 'HardDrive', 'Cloud', 'CloudUpload', 'CloudDownload',
  'Sun', 'Moon', 'Sunrise', 'Sunset', 'Wind', 'Droplet',
  'Umbrella', 'Thermometer', 'Activity', 'BarChart', 'PieChart', 'TrendingDown',
  'Plus', 'Minus', 'X', 'Check', 'ChevronRight', 'ChevronLeft',
  'ChevronUp', 'ChevronDown', 'ArrowRight', 'ArrowLeft', 'ArrowUp', 'ArrowDown',
  'RefreshCw', 'RotateCw', 'RotateCcw', 'Maximize', 'Minimize', 'ZoomIn',
  'ZoomOut', 'Move', 'Copy', 'Scissors', 'Trash', 'Trash2',
  'Edit', 'Edit2', 'Edit3', 'PenTool', 'Feather', 'Type',
  'Bold', 'Italic', 'Underline', 'AlignLeft', 'AlignCenter', 'AlignRight',
  'List', 'Grid', 'Layout', 'Sidebar', 'Menu', 'MoreVertical',
  'MoreHorizontal', 'Command', 'Hash', 'AtSign', 'Percent', 'Layers',
  'Sparkles', 'Flame', 'Lightbulb', 'Rocket', 'Flag', 'Compass'
]

// دالة للحصول على مكون الأيقونة
const getIconComponent = (iconName) => {
  return lucideIcons[iconName] || null
}

const filteredIcons = computed(() => {
  if (!searchQuery.value) return availableIcons
  return availableIcons.filter(icon => 
    icon.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const openPicker = () => {
  selectedIcon.value = props.modelValue
  showPicker.value = true
  searchQuery.value = ''
}

const closePicker = () => {
  showPicker.value = false
}

const selectIcon = (icon) => {
  emit('update:modelValue', icon)
  closePicker()
}
</script>
