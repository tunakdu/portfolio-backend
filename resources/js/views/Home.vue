<template>
  <section class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50/30 to-purple-50/30">
    <FloatingElements />

    <div v-if="loading" class="relative z-10 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600">Yükleniyor...</p>
    </div>

    <div v-else class="relative z-10 flex flex-col items-center text-center px-4 max-w-6xl mx-auto">
      <ModernLogo />

      <div class="space-y-4">
        <h2 class="text-lg sm:text-xl font-medium text-gray-600 mb-2">
          {{ settings.heroGreeting || "👋 Merhaba, Ben" }}
        </h2>

        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 bg-clip-text text-transparent">
          {{ settings.name || "Tunahan Akduhan" }}
        </h1>

        <p class="text-xl sm:text-2xl text-gray-600 font-medium">
          {{ settings.title || "Backend & API Developer" }}
        </p>
      </div>

      <p class="mt-8 max-w-2xl text-center text-gray-500 text-lg leading-relaxed">
        {{ settings.heroDescription || "Laravel, Node.js, RESTful API'lar ve modern backend teknolojileri ile güvenli ve ölçeklenebilir sistemler geliştiriyorum." }}
      </p>

      <div class="mt-10 flex flex-wrap justify-center items-center gap-4">
        <div>
          <router-link to="/contact" class="action-button-primary">
            <Sparkles class="mr-2 h-5 w-5" />
            {{ settings.contactButtonText || "İletişime Geç" }}
            <ArrowRight class="ml-2 h-5 w-5" />
          </router-link>
        </div>

        <div>
          <a :href="settings.cv_url || '/cv.pdf'" target="_blank" class="action-button-secondary">
            <Download class="mr-2 h-5 w-5" />
            {{ settings.cvButtonText || "CV'mi İndir" }}
          </a>
        </div>
      </div>

      <div class="mt-12 flex justify-center gap-8">
        <a :href="settings.github_url || '#'" target="_blank" aria-label="GitHub" class="group">
          <div class="social-icon-wrapper">
            <Github class="social-icon" />
          </div>
        </a>
        <a :href="settings.linkedin_url || '#'" target="_blank" aria-label="LinkedIn" class="group">
          <div class="social-icon-wrapper">
            <Linkedin class="social-icon" />
          </div>
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Github, Linkedin, Sparkles, ArrowRight, Download } from 'lucide-vue-next';
import ModernLogo from '../components/hero/ModernLogo.vue';
import FloatingElements from '../components/hero/FloatingElements.vue';
import axios from 'axios';

const settings = ref({});
const loading = ref(true);

onMounted(async () => {
  try {
    const response = await axios.get('/api/site-settings');
    settings.value = response.data || {};
  } catch (error) {
    console.error("Ayarlar yüklenirken bir hata oluştu:", error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.action-button-primary {
  @apply inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-300;
}
.action-button-secondary {
  @apply inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-full text-gray-800 bg-white/80 backdrop-blur-sm shadow-lg hover:shadow-xl transition-all duration-300;
}
.social-icon-wrapper {
  @apply p-3 rounded-full bg-white/80 backdrop-blur-sm shadow-lg hover:shadow-xl transition-all duration-300;
}
.social-icon {
  @apply h-6 w-6 text-gray-600 group-hover:text-gray-900 transition-colors;
}

@keyframes icon-animation-1 {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(10deg); }
  75% { transform: rotate(-10deg); }
}
.icon-animation-1 {
  animation: icon-animation-1 2s infinite ease-in-out 3s;
}

@keyframes icon-animation-2 {
  0%, 100% { transform: translateX(0); }
  50% { transform: translateX(3px); }
}
.icon-animation-2 {
  animation: icon-animation-2 1.5s infinite ease-in-out 4s;
}

@keyframes icon-animation-3 {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-2px); }
}
.icon-animation-3 {
  animation: icon-animation-3 2s infinite ease-in-out 5s;
}
</style>
