<template>
  <section class="py-24 sm:py-32 bg-gradient-to-b from-white to-slate-50" id="blog">
    <div v-if="loading" class="flex items-center justify-center min-h-[50vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Makaleler yükleniyor...</p>
      </div>
    </div>

    <div v-else class="mx-auto max-w-7xl px-6 lg:px-8">
      <!-- Header Section -->
      <div class="mx-auto max-w-4xl text-center mb-20">
        <div class="animate-fade-up">
          <span class="inline-block px-4 py-2 rounded-full bg-purple-100 text-purple-600 text-sm font-medium mb-8">
            📝 Blog
          </span>
          <h2 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
            Yazılım ve Teknoloji 
            <span class="bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
              Notlarım
            </span>
          </h2>
          <p class="text-xl text-gray-600 leading-relaxed mb-8">
            Sektördeki yenilikler, öğrendiğim teknolojiler ve kariyerim boyunca edindiğim tecrübeler üzerine yazdığım makaleler.
          </p>
          
          <!-- Stats -->
          <div class="flex justify-center items-center space-x-8 text-sm text-gray-500 mb-12">
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-green-500 rounded-full"></div>
              <span>{{ articles.length }} Makale</span>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
              <span>{{ totalReadTime }} dk okuma</span>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
              <span>Sürekli güncelleniyor</span>
            </div>
          </div>
        </div>
      </div>


      <!-- Articles Grid -->
      <div v-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(article, index) in articles"
          :key="article.slug"
          class="project-card group cursor-pointer transform transition-all duration-500 hover:scale-105"
          :class="`animate-fade-up-delay-${index % 3}`"
          @click="goToArticle(article.slug)"
        >
          <div class="relative overflow-hidden rounded-t-2xl">
            <img
              v-if="article.cover_image"
              :src="article.cover_image"
              :alt="article.title"
              class="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-110"
            />
            <div v-else class="w-full h-48 bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
              <FileText class="w-16 h-16 text-white opacity-50" />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </div>
          <div class="p-6 bg-white">
            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-purple-600 transition-colors duration-300">
              {{ article.title }}
            </h3>

            <!-- Date -->
            <div class="flex items-center text-gray-500 text-sm mb-4">
              <Calendar class="w-4 h-4 mr-2" />
              <span>{{ formatDate(article.published_at || article.created_at) }}</span>
            </div>

            <!-- Read More -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
              <div class="flex items-center text-purple-600 font-semibold group-hover:text-purple-700 transition-colors">
                <span>Devamını Oku</span>
                <ArrowRight class="w-4 h-4 ml-2 transform transition-transform group-hover:translate-x-1" />
              </div>
              <div class="text-xs text-gray-400">
                {{ getReadTime(article.content || '') }} dk okuma
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20">
        <div class="animate-fade-up">
          <div class="w-32 h-32 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-8">
            <FileText class="w-16 h-16 text-purple-500" />
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">Henüz makale yok</h3>
          <p class="text-lg text-gray-600 max-w-md mx-auto mb-8">
            Yakında yazılım ve teknoloji hakkında ilginç makaleler paylaşacağım.
          </p>
          <button 
            @click="router.push('/')"
            class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-colors"
          >
            <ArrowLeft class="w-4 h-4 mr-2" />
            Ana Sayfaya Dön
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, FileText, Calendar, ArrowLeft } from 'lucide-vue-next';
import axios from 'axios';

const articles = ref([]);
const loading = ref(true);
const router = useRouter();

const fetchArticles = async () => {
    try {
        const response = await axios.get('/api/articles');
        articles.value = response.data.data;
    } catch (error) {
        console.error("Makaleler alınamadı:", error);
        articles.value = [];
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

const getReadTime = (content) => {
    // Basit bir okuma süresi hesaplama (ortalama 200 kelime/dakika)
    if (!content) return 1;
    const wordCount = content.replace(/<[^>]*>/g, '').split(' ').length;
    return Math.max(1, Math.ceil(wordCount / 200));
};

const totalReadTime = computed(() => {
    return articles.value.reduce((total, article) => {
        return total + getReadTime(article.content || '');
    }, 0);
});

const goToArticle = (slug) => {
    router.push({ name: 'BlogDetail', params: { slug } });
};

onMounted(fetchArticles);
</script>

<style scoped>
.project-card {
  @apply bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.project-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.line-clamp-2 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

/* Animation Classes */
.animate-fade-up {
  animation: fadeUp 0.6s ease-out;
}

.animate-fade-up-delay-0 {
  animation: fadeUp 0.6s ease-out 0.1s both;
}

.animate-fade-up-delay-1 {
  animation: fadeUp 0.6s ease-out 0.2s both;
}

.animate-fade-up-delay-2 {
  animation: fadeUp 0.6s ease-out 0.3s both;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
