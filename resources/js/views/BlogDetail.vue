<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <button
              @click="router.push('/blog')"
              class="text-gray-600 hover:text-gray-900 font-medium"
            >
              ← Blog
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Makale</h1>
          </div>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Makale yükleniyor...</p>
      </div>
    </div>
    <!-- Main Content -->
    <main v-else class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Article Content -->
      <article v-if="article" class="modern-card">

          <!-- Article Header -->
          <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4">
              {{ article.title }}
            </h1>
            <div class="flex items-center text-gray-600 mb-6">
              <Calendar class="w-4 h-4 mr-2" />
              <time>{{ formatDate(article.published_at) }}</time>
            </div>
          </header>

          <!-- Cover Image -->
          <div v-if="article.cover_image" class="mb-12">
            <img 
              :src="article.cover_image" 
              :alt="article.title" 
              class="w-full h-auto object-cover rounded-2xl shadow-lg"
            >
          </div>

          <!-- Article Content -->
          <div class="prose prose-lg prose-blue max-w-none" v-html="article.content"></div>
        </article>
      <!-- Error State -->
      <div v-else class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <AlertCircle class="w-12 h-12 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Makale Bulunamadı</h3>
        <p class="text-gray-500 mb-6">
          Aradığınız makaleye ulaşılamıyor.
        </p>
        <button 
          @click="router.push('/blog')" 
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
        >
          Blog Sayfasına Dön
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowLeft, Calendar, AlertCircle } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({ slug: String });
const router = useRouter();
const article = ref(null);
const loading = ref(true);

const fetchArticle = async () => {
    try {
        const response = await axios.get(`/api/articles/${props.slug}`);
        article.value = response.data;
    } catch (error) {
        console.error("Makale alınamadı:", error);
        article.value = null;
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

onMounted(fetchArticle);
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 p-8;
}

/* Prose styling */
:deep(.prose) {
  @apply text-gray-700;
}

:deep(.prose h1),
:deep(.prose h2),
:deep(.prose h3),
:deep(.prose h4),
:deep(.prose h5),
:deep(.prose h6) {
  @apply text-gray-900 font-bold;
}

:deep(.prose h1) {
  @apply text-3xl mb-6 mt-8;
}

:deep(.prose h2) {
  @apply text-2xl mb-4 mt-8;
}

:deep(.prose h3) {
  @apply text-xl mb-3 mt-6;
}

:deep(.prose p) {
  @apply mb-4 leading-relaxed;
}

:deep(.prose a) {
  @apply text-blue-600 hover:text-blue-700 font-medium no-underline hover:underline transition-colors;
}

:deep(.prose img) {
  @apply rounded-xl shadow-lg my-8;
}

:deep(.prose blockquote) {
  @apply border-l-4 border-blue-200 pl-4 italic text-gray-600 my-6;
}

:deep(.prose code) {
  @apply bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm;
}

:deep(.prose pre) {
  @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto my-6;
}

:deep(.prose ul),
:deep(.prose ol) {
  @apply mb-4 pl-6;
}

:deep(.prose li) {
  @apply mb-2;
}
</style>
