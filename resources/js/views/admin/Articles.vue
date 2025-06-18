<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <button
              @click="router.push('/admin/dashboard')"
              class="text-gray-600 hover:text-gray-900 font-medium"
            >
              ← Dashboard
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Makaleler</h1>
          </div>
          <button
            @click="router.push('/admin/articles/new')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
          >
            <Plus class="w-4 h-4" />
            <span>Yeni Makale</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="isLoading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Makaleler yükleniyor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Empty State -->
      <div v-if="articles.length === 0" class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <FileText class="w-12 h-12 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz makale yok</h3>
        <p class="text-gray-500 mb-6">İlk makalenizi ekleyerek başlayın</p>
        <button
          @click="router.push('/admin/articles/new')"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
        >
          İlk Makaleyi Ekle
        </button>
      </div>

      <!-- Articles Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="article in articles"
          :key="article.id"
          class="modern-card overflow-hidden"
        >
          <img
            v-if="article.cover_image"
            :src="article.cover_image"
            :alt="article.title"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                {{ article.title }}
              </h3>
              <span
                v-if="article.is_published"
                class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium ml-2 flex-shrink-0"
              >
                Yayında
              </span>
              <span
                v-else
                class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium ml-2 flex-shrink-0"
              >
                Taslak
              </span>
            </div>

            <p class="text-gray-600 text-sm mb-4">
              {{ formatDate(article.created_at) }}
            </p>

            <div class="flex items-center justify-between">
              <div class="flex space-x-2">
                <a
                  v-if="article.is_published"
                  :href="`/blog/${article.slug}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-blue-600 hover:text-blue-700"
                >
                  <ExternalLink class="w-4 h-4" />
                </a>
              </div>

              <div class="flex space-x-2">
                <button
                  @click="router.push(`/admin/articles/edit/${article.slug}`)"
                  class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  <Edit class="w-4 h-4" />
                </button>
                <button
                  @click="deleteArticle(article.slug)"
                  class="p-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus, Edit, Trash2, ExternalLink, FileText } from 'lucide-vue-next';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();

const articles = ref([]);
const isLoading = ref(true);

const fetchArticles = async () => {
    try {
        const response = await axios.get('/api/articles');
        articles.value = response.data.data;
    } catch (error) {
        console.error('Makaleler getirilirken hata oluştu:', error);
        articles.value = [];
    } finally {
        isLoading.value = false;
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const deleteArticle = async (slug) => {
    const result = await Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu makaleyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: 'İptal'
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/articles/${slug}`);
            
            // Remove from local state
            articles.value = articles.value.filter(a => a.slug !== slug);
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Makale başarıyla silindi!',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } catch (error) {
            console.error('Makale silinirken hata:', error);
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Makale silinirken bir sorun oluştu!',
                confirmButtonColor: '#ef4444'
            });
        }
    }
};

onMounted(fetchArticles);
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200;
}

.line-clamp-2 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>
