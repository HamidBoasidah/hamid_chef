<template>
  <ThemeProvider>
    <SidebarProvider>
      <GlobalLoadingOverlay />
  <slot />
    </SidebarProvider>
  </ThemeProvider>
</template>

<script setup lang="ts">
import ThemeProvider from '@/Components/layout/ThemeProvider.vue'
import SidebarProvider from '@/Components/layout/SidebarProvider.vue'
import GlobalLoadingOverlay from '@/Components/common/GlobalLoadingOverlay.vue'
</script>
