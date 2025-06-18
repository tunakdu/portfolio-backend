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
            <h1 class="text-2xl font-bold text-gray-900">Projeler</h1>
          </div>
          <button
            @click="router.push('/admin/projects/new')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
          >
            <Plus class="w-4 h-4" />
            <span>Yeni Proje</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="isLoading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Projeler yükleniyor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Empty State -->
      <div v-if="projects.length === 0" class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <Plus class="w-12 h-12 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz proje yok</h3>
        <p class="text-gray-500 mb-6">İlk projenizi ekleyerek başlayın</p>
        <button
          @click="router.push('/admin/projects/new')"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
        >
          İlk Projeyi Ekle
        </button>
      </div>

      <!-- Projects Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="project in projects"
          :key="project.id"
          class="modern-card overflow-hidden"
        >
          <img
            v-if="project.image"
            :src="project.image"
            :alt="project.title"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                {{ project.title }}
              </h3>
              <span
                v-if="project.featured"
                class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium"
              >
                Öne Çıkan
              </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
              {{ project.description }}
            </p>
            
            <div class="mb-4">
              <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium mb-2">
                {{ project.category }}
              </span>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="tech in project.technologies.slice(0, 3)"
                  :key="tech"
                  class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                >
                  {{ tech }}
                </span>
                <span
                  v-if="project.technologies.length > 3"
                  class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                >
                  +{{ project.technologies.length - 3 }}
                </span>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex space-x-2">
                <a
                  v-if="project.live_url"
                  :href="project.live_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-blue-600 hover:text-blue-700"
                >
                  <ExternalLink class="w-4 h-4" />
                </a>
                <a
                  v-if="project.github_url"
                  :href="project.github_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-gray-600 hover:text-gray-700"
                >
                  <Eye class="w-4 h-4" />
                </a>
              </div>
              
              <div class="flex space-x-2">
                <button
                  @click="router.push(`/admin/projects/${project.id}/edit`)"
                  class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  <Edit class="w-4 h-4" />
                </button>
                <button
                  @click="handleDelete(project.id)"
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
import { Plus, Edit, Trash2, Eye, ExternalLink } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const { checkAuth } = useAuth();
const projects = ref([]);
const isLoading = ref(true);

const fetchProjects = async () => {
  try {
    const response = await axios.get('/api/projects');
    const data = response.data;
    
    // Technologies field'ini parse et
    const parsedProjects = data.map((project) => ({
      ...project,
      technologies: typeof project.technologies === 'string' 
        ? JSON.parse(project.technologies) 
        : (project.technologies || [])
    }));
    projects.value = parsedProjects;
  } catch (error) {
    console.error("Error fetching projects:", error);
    projects.value = [];
  } finally {
    isLoading.value = false;
  }
};

const handleDelete = async (id) => {
  const result = await Swal.fire({
    title: 'Emin misiniz?',
    text: 'Bu projeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Evet, Sil!',
    cancelButtonText: 'İptal'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/projects/${id}`);
      
      // Remove from local state
      projects.value = projects.value.filter(p => p.id !== id);
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Proje başarıyla silindi!',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    } catch (error) {
      console.error("Error deleting project:", error);
      Swal.fire({
        icon: 'error',
        title: 'Hata!',
        text: 'Proje silinirken bir hata oluştu!',
        confirmButtonColor: '#ef4444'
      });
    }
  }
};

onMounted(async () => {
  await checkAuth();
  await fetchProjects();
});
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200;
}

.line-clamp-1 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}

.line-clamp-2 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>