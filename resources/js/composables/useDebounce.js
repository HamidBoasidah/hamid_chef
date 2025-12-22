import { ref, onUnmounted } from 'vue'

/**
 * Composable for debouncing function calls
 * @param {Function} fn - Function to debounce
 * @param {number} delay - Delay in milliseconds (default: 300)
 * @returns {Object} - { debouncedFn, isLoading, cancel }
 */
export function useDebounce(fn, delay = 300) {
  const isLoading = ref(false)
  let timeoutId = null

  const debouncedFn = (...args) => {
    // Cancel previous timeout
    clearTimeout(timeoutId)
    
    // Set loading state
    isLoading.value = true
    
    // Set new timeout
    timeoutId = setTimeout(async () => {
      try {
        await fn(...args)
      } catch (error) {
        console.error('Debounced function error:', error)
      } finally {
        isLoading.value = false
      }
    }, delay)
  }

  const cancel = () => {
    clearTimeout(timeoutId)
    isLoading.value = false
  }

  // Cleanup on component unmount
  onUnmounted(() => {
    cancel()
  })

  return { 
    debouncedFn, 
    isLoading, 
    cancel 
  }
}

/**
 * Simple debounce utility function
 * @param {Function} fn - Function to debounce
 * @param {number} delay - Delay in milliseconds
 * @returns {Function} - Debounced function
 */
export function debounce(fn, delay = 300) {
  let timeoutId = null
  
  return function (...args) {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => fn.apply(this, args), delay)
  }
}

/**
 * Throttle utility function (alternative to debounce)
 * @param {Function} fn - Function to throttle
 * @param {number} delay - Delay in milliseconds
 * @returns {Function} - Throttled function
 */
export function throttle(fn, delay = 300) {
  let lastCall = 0
  
  return function (...args) {
    const now = Date.now()
    
    if (now - lastCall >= delay) {
      lastCall = now
      return fn.apply(this, args)
    }
  }
}