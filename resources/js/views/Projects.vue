<template>
  <section class="py-24 sm:py-32 bg-gradient-to-b from-white to-slate-50" id="projects">
    <div v-if="loading" class="flex items-center justify-center min-h-[50vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Projeler yükleniyor...</p>
      </div>
    </div>

    <div v-else class="mx-auto max-w-7xl px-6 lg:px-8">
      <!-- Header Section -->
      <div class="mx-auto max-w-4xl text-center mb-20">
        <div class="animate-fade-up">
          <span class="inline-block px-4 py-2 rounded-full bg-blue-100 text-blue-600 text-sm font-medium mb-8">
            💼 Portföyüm
          </span>
          <h2 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
            Gerçekleştirdiğim 
            <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
              Projeler
            </span>
          </h2>
          <p class="text-xl text-gray-600 leading-relaxed mb-8">
            Modern teknolojiler kullanarak geliştirdiğim web uygulamaları, mobil uygulamalar ve backend sistemler.
          </p>
          
          <!-- Stats -->
          <div class="flex justify-center items-center space-x-8 text-sm text-gray-500 mb-12">
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-green-500 rounded-full"></div>
              <span>{{ projects.filter(p => p.status === 'completed').length }} Tamamlandı</span>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
              <span>{{ projects.filter(p => p.status === 'in_progress').length }} Devam Ediyor</span>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
              <span>{{ featuredCount }} Öne Çıkan</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Category Filter -->
      <div class="flex justify-center mb-12">
        <div class="bg-white rounded-2xl p-2 shadow-lg border border-gray-200">
          <div class="flex flex-wrap gap-2">
            <button
              @click="selectedCategory = null"
              :class="`px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300 ${
                selectedCategory === null
                  ? 'bg-blue-600 text-white shadow-md'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
              }`"
            >
              🌟 Tümü ({{ projects.length }})
            </button>
            <button
              v-for="category in categories"
              :key="category.id"
              @click="selectedCategory = category.name"
              :class="`px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300 flex items-center space-x-2 ${
                selectedCategory === category.name
                  ? 'bg-blue-600 text-white shadow-md'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
              }`"
            >
              <span>{{ getCategoryIcon(category.icon) }}</span>
              <span>{{ category.name }} ({{ getProjectCountByCategory(category.name) }})</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Projects Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(project, index) in filteredProjects"
          :key="project.id"
          class="modern-card group hover:scale-105 transition-all duration-500 overflow-hidden cursor-pointer animate-fade-up"
          :style="{ animationDelay: `${index * 0.1}s` }"
          @click="openProjectModal(project)"
        >
          <!-- Image Section -->
          <div class="relative overflow-hidden">
            <img
              v-if="project.image"
              :src="project.image"
              :alt="project.title"
              class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <div v-else class="w-full h-56 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
              <Code class="w-16 h-16 text-white" />
            </div>
            
            <!-- Badges -->
            <div class="absolute top-4 left-4 right-4 flex justify-between items-start">
              <span :class="`inline-block px-3 py-1 rounded-full text-xs font-medium shadow-lg ${getCategoryColor(project.category)}`">
                {{ getCategoryIcon(getCategoryIconName(project.category)) }} {{ project.category }}
              </span>
              <div class="flex space-x-2">
                <span v-if="project.featured" class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium shadow-lg">
                  ⭐ Öne Çıkan
                </span>
                <span :class="`px-2 py-1 rounded-full text-xs font-medium shadow-lg ${getStatusColor(project.status)}`">
                  {{ getStatusText(project.status) }}
                </span>
              </div>
            </div>

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
              <div class="transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                <div class="bg-white rounded-full p-3 shadow-lg">
                  <Eye class="w-5 h-5 text-gray-700" />
                </div>
              </div>
            </div>
          </div>
          
          <!-- Content Section -->
          <div class="p-6">
            <div class="mb-4">
              <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                {{ project.title }}
              </h3>
              <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
                {{ project.description }}
              </p>
            </div>
            
            <!-- Technologies -->
            <div class="mb-6">
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="tech in project.technologies.slice(0, 3)"
                  :key="tech"
                  class="px-3 py-1 bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 rounded-full text-xs font-medium border border-gray-200 hover:border-blue-300 transition-colors"
                >
                  {{ tech }}
                </span>
                <span
                  v-if="project.technologies.length > 3"
                  class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium border border-blue-200"
                >
                  +{{ project.technologies.length - 3 }} daha
                </span>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between">
              <div class="flex space-x-3">
                <a
                  v-if="project.live_url"
                  :href="project.live_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  @click.stop
                  class="flex items-center space-x-1 text-blue-600 hover:text-blue-700 transition-colors"
                >
                  <ExternalLink class="w-4 h-4" />
                  <span class="text-sm font-medium">Demo</span>
                </a>
                <a
                  v-if="project.github_url"
                  :href="project.github_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  @click.stop
                  class="flex items-center space-x-1 text-gray-600 hover:text-gray-700 transition-colors"
                >
                  <Github class="w-4 h-4" />
                  <span class="text-sm font-medium">Kod</span>
                </a>
              </div>
              
              <div class="text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors flex items-center space-x-1">
                <span>Detaylar</span>
                <ChevronRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProjects.length === 0" class="text-center py-20">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <Search class="w-12 h-12 text-gray-400" />
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Proje bulunamadı</h3>
        <p class="text-gray-600">
          {{ selectedCategory ? `"${selectedCategory}" kategorisinde proje bulunmuyor.` : 'Henüz proje eklenmemiş.' }}
        </p>
      </div>
    </div>

    <!-- Project Detail Modal -->
    <div 
      v-if="selectedProject" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="closeModal"
    >
      <div
        class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-scale-up"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="relative">
          <img
            v-if="selectedProject.image"
            :src="selectedProject.image"
            :alt="selectedProject.title"
            class="w-full h-80 object-cover"
          />
          <div v-else class="w-full h-80 bg-gradient-to-br from-blue-400 to-purple-500"></div>
          
          <div class="absolute inset-0 bg-black bg-opacity-40"></div>
          
          <button
            @click="closeModal"
            class="absolute top-4 right-4 w-10 h-10 bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 rounded-full flex items-center justify-center transition-colors text-white"
          >
            <X class="w-5 h-5" />
          </button>
          
          <div class="absolute bottom-6 left-6 right-6 text-white">
            <div class="flex items-center space-x-3 mb-4">
              <span :class="`px-3 py-1 rounded-full text-xs font-medium ${getCategoryColor(selectedProject.category)} bg-opacity-90`">
                {{ getCategoryIcon(getCategoryIconName(selectedProject.category)) }} {{ selectedProject.category }}
              </span>
              <span v-if="selectedProject.featured" class="bg-yellow-500 bg-opacity-90 text-white px-3 py-1 rounded-full text-xs font-medium">
                ⭐ Öne Çıkan
              </span>
            </div>
            <h2 class="text-3xl font-bold mb-2">{{ selectedProject.title }}</h2>
            <p class="text-lg text-white text-opacity-90">{{ selectedProject.description }}</p>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="p-8">
          <!-- Project Details -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2">
              <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <FileText class="w-5 h-5 mr-2" />
                Proje Detayları
              </h3>
              <div class="prose max-w-none text-gray-700 leading-relaxed">
                {{ selectedProject.content || selectedProject.description }}
              </div>
            </div>
            
            <div>
              <!-- Project Info -->
              <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <h4 class="font-semibold text-gray-900 mb-4">Proje Bilgileri</h4>
                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Durum:</span>
                    <span :class="`font-medium ${getStatusColor(selectedProject.status).includes('green') ? 'text-green-700' : 'text-yellow-700'}`">
                      {{ getStatusText(selectedProject.status) }}
                    </span>
                  </div>
                  <div v-if="selectedProject.start_date" class="flex justify-between">
                    <span class="text-gray-600">Başlangıç:</span>
                    <span class="font-medium text-gray-900">{{ formatDate(selectedProject.start_date) }}</span>
                  </div>
                  <div v-if="selectedProject.end_date" class="flex justify-between">
                    <span class="text-gray-600">Bitiş:</span>
                    <span class="font-medium text-gray-900">{{ formatDate(selectedProject.end_date) }}</span>
                  </div>
                  <div v-if="selectedProject.start_date && selectedProject.end_date" class="flex justify-between">
                    <span class="text-gray-600">Süre:</span>
                    <span class="font-medium text-gray-900">{{ getProjectDuration(selectedProject.start_date, selectedProject.end_date) }}</span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="space-y-3">
                <a
                  v-if="selectedProject.live_url"
                  :href="selectedProject.live_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl font-medium transition-colors flex items-center justify-center space-x-2"
                >
                  <ExternalLink class="w-4 h-4" />
                  <span>Canlı Demo</span>
                </a>
                <a
                  v-if="selectedProject.github_url"
                  :href="selectedProject.github_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full bg-gray-800 hover:bg-gray-900 text-white py-3 px-4 rounded-xl font-medium transition-colors flex items-center justify-center space-x-2"
                >
                  <Github class="w-4 h-4" />
                  <span>Kaynak Kodu</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Technologies -->
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
              <Code class="w-5 h-5 mr-2" />
              Kullanılan Teknolojiler
            </h3>
            <div class="flex flex-wrap gap-3">
              <span
                v-for="tech in selectedProject.technologies"
                :key="tech"
                class="px-4 py-2 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 text-blue-800 rounded-lg font-medium hover:from-blue-100 hover:to-purple-100 transition-colors"
              >
                {{ tech }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { ExternalLink, Github, Calendar, Star, Code, Eye, Search, X, ChevronRight, FileText } from 'lucide-vue-next';
import axios from 'axios';

const projects = ref([]);
const categories = ref([]);
const loading = ref(true);
const selectedCategory = ref(null);
const selectedProject = ref(null);

const featuredCount = computed(() => projects.value.filter(p => p.featured).length);

const filteredProjects = computed(() => {
  if (!selectedCategory.value) {
    return projects.value;
  }
  return projects.value.filter(p => p.category === selectedCategory.value);
});

const getCategoryIcon = (iconName) => {
  const icons = {
    Globe: '🌐',
    Smartphone: '📱',
    Monitor: '🖥️',
    Server: '🖥️',
    Gamepad2: '🎮',
    Brain: '🧠',
    Folder: '📁'
  };
  return icons[iconName] || '📁';
};

const getCategoryIconName = (categoryName) => {
  const categoryIcons = {
    'Web Development': 'Globe',
    'Mobile Development': 'Smartphone',
    'Desktop Development': 'Monitor',
    'Backend Development': 'Server',
    'Game Development': 'Gamepad2',
    'AI & Machine Learning': 'Brain'
  };
  return categoryIcons[categoryName] || 'Folder';
};

const getCategoryColor = (categoryName) => {
  const colors = {
    'Web Development': 'bg-blue-100 text-blue-800',
    'Mobile Development': 'bg-green-100 text-green-800',
    'Desktop Development': 'bg-purple-100 text-purple-800',
    'Backend Development': 'bg-orange-100 text-orange-800',
    'Game Development': 'bg-red-100 text-red-800',
    'AI & Machine Learning': 'bg-indigo-100 text-indigo-800'
  };
  return colors[categoryName] || 'bg-gray-100 text-gray-800';
};

const getStatusColor = (status) => {
  const colors = {
    'completed': 'bg-green-100 text-green-800',
    'in_progress': 'bg-yellow-100 text-yellow-800',
    'planned': 'bg-blue-100 text-blue-800',
    'on_hold': 'bg-gray-100 text-gray-800'
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const texts = {
    'completed': 'Tamamlandı',
    'in_progress': 'Devam Ediyor',
    'planned': 'Planlandı',
    'on_hold': 'Beklemede'
  };
  return texts[status] || 'Bilinmiyor';
};

const getProjectCountByCategory = (categoryName) => {
  return projects.value.filter(p => p.category === categoryName).length;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('tr-TR', options);
};

const getProjectDuration = (startDate, endDate) => {
  const start = new Date(startDate);
  const end = new Date(endDate);
  const diffTime = Math.abs(end - start);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  const months = Math.floor(diffDays / 30);
  const days = diffDays % 30;
  
  if (months > 0) {
    return `${months} ay ${days} gün`;
  }
  return `${days} gün`;
};

const openProjectModal = (project) => {
  selectedProject.value = project;
};

const closeModal = () => {
  selectedProject.value = null;
};

// ESC tuşu ile modal kapatma
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && selectedProject.value) {
    closeModal();
  }
};

const fetchData = async () => {
  try {
    // Fetch projects
    const projectsResponse = await axios.get('/api/projects');
    projects.value = projectsResponse.data.map(project => ({
      ...project,
      technologies: typeof project.technologies === 'string' 
        ? JSON.parse(project.technologies) 
        : (project.technologies || [])
    }));

    // Fetch categories
    const categoriesResponse = await axios.get('/api/categories');
    categories.value = categoriesResponse.data;
  } catch (error) {
    console.error('Data fetch error:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await fetchData();
  document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyDown);
});
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-3xl shadow-lg border border-gray-200/80;
}

.line-clamp-2 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.line-clamp-3 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}

@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scale-up {
  from {
    opacity: 0;
    transform: scale(0.8) translateY(50px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-fade-up {
  animation: fade-up 0.6s ease-out forwards;
}

.animate-scale-up {
  animation: scale-up 0.3s ease-out forwards;
}
</style>