<template>
  <div>
    <router-view v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
    <Dock />
    <!-- Dock için bottom padding -->
    <div class="h-20"></div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Dock from './Dock.vue';
import { setupRouterTracking, setupLinkTracking } from '../plugins/analytics';

const router = useRouter();

onMounted(() => {
  // Setup analytics tracking
  setupRouterTracking(router);
  setupLinkTracking();
  console.log('🚀 Portfolio app loaded with Analytics');
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
