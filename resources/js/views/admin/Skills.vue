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
            <h1 class="text-2xl font-bold text-gray-900">Yetenekler</h1>
          </div>
          <button
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
          >
            <Plus class="w-4 h-4" />
            <span>Yeni Yetenek</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="isLoading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Yetenekler yükleniyor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Empty State -->
      <div v-if="skills.length === 0" class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <Plus class="w-12 h-12 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz yetenek yok</h3>
        <p class="text-gray-500 mb-6">İlk yeteneğinizi ekleyerek başlayın</p>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
        >
          İlk Yeteneği Ekle
        </button>
      </div>

      <!-- Skills Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="skill in skills"
          :key="skill.id"
          class="modern-card p-6 group hover:scale-105 transition-all duration-300"
        >
          <!-- Icon -->
          <div :class="`w-14 h-14 rounded-xl bg-gradient-to-r ${skill.color} p-3 mb-4 group-hover:scale-110 transition-transform duration-300`">
            <div class="w-full h-full bg-white rounded-lg flex items-center justify-center">
              <span class="text-xl">{{ getSkillIcon(skill.icon) }}</span>
            </div>
          </div>

          <!-- Title & Rating -->
          <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-bold text-gray-900 leading-tight">
              {{ skill.title }}
            </h3>
            <div class="flex items-center space-x-1 ml-2">
              <div
                v-for="i in 5"
                :key="i"
                :class="`w-2 h-2 rounded-full ${
                  i <= skill.proficiency_level ? 'bg-yellow-400' : 'bg-gray-300'
                }`"
              />
            </div>
          </div>

          <!-- Description -->
          <p class="text-gray-600 text-sm mb-4 leading-relaxed line-clamp-3">
            {{ skill.description }}
          </p>

          <!-- Technologies Preview -->
          <div class="flex flex-wrap gap-1 mb-4">
            <span
              v-for="tech in skill.technologies.slice(0, 3)"
              :key="tech"
              class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium"
            >
              {{ tech }}
            </span>
            <span
              v-if="skill.technologies.length > 3"
              class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium"
            >
              +{{ skill.technologies.length - 3 }} daha
            </span>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <span :class="`inline-block w-2 h-2 rounded-full ${skill.is_active ? 'bg-green-500' : 'bg-red-500'}`"></span>
              <span class="text-xs text-gray-500">{{ skill.is_active ? 'Aktif' : 'Pasif' }}</span>
            </div>
            
            <div class="flex space-x-2">
              <button
                @click="editSkill(skill)"
                class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50"
              >
                <Edit class="w-4 h-4" />
              </button>
              <button
                @click="handleDelete(skill.id)"
                class="p-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Create/Edit Modal -->
    <div 
      v-if="showCreateModal || editingSkill" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="closeModal"
    >
      <div
        class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">
              {{ editingSkill ? 'Yetenek Düzenle' : 'Yeni Yetenek Ekle' }}
            </h2>
            <button
              @click="closeModal"
              class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors"
            >
              <span class="text-gray-600">✕</span>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Title -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Başlık *
              </label>
              <input
                v-model="formData.title"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                placeholder="Yetenek başlığı"
                required
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Açıklama *
              </label>
              <textarea
                v-model="formData.description"
                rows="3"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"
                placeholder="Yetenek açıklaması"
                required
              />
            </div>

            <!-- Icon & Color -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  İkon *
                </label>
                <select
                  v-model="formData.icon"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  required
                >
                  <option value="">İkon seçiniz</option>
                  <option value="Server">🖥️ Server</option>
                  <option value="Code">💻 Code</option>
                  <option value="Database">🗄️ Database</option>
                  <option value="Globe">🌐 Globe</option>
                  <option value="Cloud">☁️ Cloud</option>
                  <option value="Search">🔍 Search</option>
                  <option value="Shield">🛡️ Shield</option>
                  <option value="Zap">⚡ Zap</option>
                  <option value="Tool">🔧 Tool</option>
                  <option value="Brain">🧠 Brain</option>
                  <option value="Settings">⚙️ Settings</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Renk *
                </label>
                <select
                  v-model="formData.color"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  required
                >
                  <option value="">Renk seçiniz</option>
                  <option value="from-blue-500 to-blue-600">🔵 Mavi</option>
                  <option value="from-green-500 to-green-600">🟢 Yeşil</option>
                  <option value="from-purple-500 to-purple-600">🟣 Mor</option>
                  <option value="from-orange-500 to-orange-600">🟠 Turuncu</option>
                  <option value="from-red-500 to-red-600">🔴 Kırmızı</option>
                  <option value="from-indigo-500 to-indigo-600">🟦 İndigo</option>
                  <option value="from-pink-500 to-pink-600">🩷 Pembe</option>
                  <option value="from-yellow-500 to-yellow-600">🟡 Sarı</option>
                </select>
              </div>
            </div>

            <!-- Proficiency Level & Order -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Yeterlilik Seviyesi *
                </label>
                <select
                  v-model="formData.proficiency_level"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  required
                >
                  <option value="1">⭐ 1 - Başlangıç</option>
                  <option value="2">⭐⭐ 2 - Temel</option>
                  <option value="3">⭐⭐⭐ 3 - Orta</option>
                  <option value="4">⭐⭐⭐⭐ 4 - İleri</option>
                  <option value="5">⭐⭐⭐⭐⭐ 5 - Uzman</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Sıralama
                </label>
                <input
                  v-model="formData.order"
                  type="number"
                  min="0"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  placeholder="0"
                />
              </div>
            </div>

            <!-- Technologies -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Teknolojiler
              </label>
              <div class="flex flex-wrap gap-2 mb-3">
                <span
                  v-for="(tech, index) in formData.technologies"
                  :key="index"
                  class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center space-x-2"
                >
                  <span>{{ tech }}</span>
                  <button
                    type="button"
                    @click="removeTechnology(index)"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    <X class="w-4 h-4" />
                  </button>
                </span>
              </div>
              <div class="flex space-x-2">
                <input
                  v-model="newTechnology"
                  @keyup.enter="addTechnology"
                  class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  placeholder="Teknoloji adı (Enter ile ekle)"
                />
                <button
                  type="button"
                  @click="addTechnology"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
                >
                  <Plus class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center space-x-2">
              <input
                v-model="formData.is_active"
                type="checkbox"
                id="is_active"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <label for="is_active" class="text-sm font-medium text-gray-700">
                Aktif
              </label>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700"
              >
                İptal
              </button>
              <button
                type="submit"
                :disabled="isSaving"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 disabled:opacity-50"
              >
                <Save class="w-4 h-4" />
                <span>{{ isSaving ? 'Kaydediliyor...' : (editingSkill ? 'Güncelle' : 'Kaydet') }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus, Edit, Trash2, Save, X } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const { checkAuth } = useAuth();

const skills = ref([]);
const isLoading = ref(true);
const isSaving = ref(false);
const showCreateModal = ref(false);
const editingSkill = ref(null);
const newTechnology = ref('');

const formData = ref({
  title: '',
  description: '',
  icon: '',
  color: '',
  technologies: [],
  proficiency_level: 3,
  order: 0,
  is_active: true
});

const getSkillIcon = (iconName) => {
  const icons = {
    Database: '🗄️',
    Code: '💻',
    Smartphone: '📱',
    Globe: '🌐',
    Search: '🔍',
    Server: '🖥️',
    Cloud: '☁️',
    Shield: '🛡️',
    Zap: '⚡',
    Tool: '🔧',
    Brain: '🧠',
    Settings: '⚙️'
  };
  return icons[iconName] || '💻';
};

const fetchSkills = async () => {
  try {
    const response = await axios.get('/api/v1/admin/skills');
    skills.value = response.data;
  } catch (error) {
    console.error('Error fetching skills:', error);
    skills.value = [];
  } finally {
    isLoading.value = false;
  }
};

const addTechnology = () => {
  if (newTechnology.value.trim() && !formData.value.technologies.includes(newTechnology.value.trim())) {
    formData.value.technologies.push(newTechnology.value.trim());
    newTechnology.value = '';
  }
};

const removeTechnology = (index) => {
  formData.value.technologies.splice(index, 1);
};

const resetForm = () => {
  formData.value = {
    title: '',
    description: '',
    icon: '',
    color: '',
    technologies: [],
    proficiency_level: 3,
    order: 0,
    is_active: true
  };
  newTechnology.value = '';
};

const closeModal = () => {
  showCreateModal.value = false;
  editingSkill.value = null;
  resetForm();
};

const editSkill = (skill) => {
  editingSkill.value = skill;
  formData.value = {
    title: skill.title,
    description: skill.description,
    icon: skill.icon,
    color: skill.color,
    technologies: [...skill.technologies],
    proficiency_level: skill.proficiency_level,
    order: skill.order,
    is_active: skill.is_active
  };
};

const handleSubmit = async () => {
  isSaving.value = true;
  try {
    if (editingSkill.value) {
      // Update existing skill
      await axios.put(`/api/v1/admin/skills/${editingSkill.value.id}`, formData.value);
      
      // Update local state
      const index = skills.value.findIndex(s => s.id === editingSkill.value.id);
      if (index !== -1) {
        skills.value[index] = { ...editingSkill.value, ...formData.value };
      }
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Yetenek başarıyla güncellendi!',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    } else {
      // Create new skill
      const response = await axios.post('/api/v1/admin/skills', formData.value);
      skills.value.push(response.data);
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Yetenek başarıyla eklendi!',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    }
    
    closeModal();
  } catch (error) {
    console.error('Error saving skill:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Yetenek kaydedilirken bir hata oluştu!',
      confirmButtonColor: '#ef4444'
    });
  } finally {
    isSaving.value = false;
  }
};

const handleDelete = async (skillId) => {
  const result = await Swal.fire({
    title: 'Emin misiniz?',
    text: 'Bu yeteneği silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Evet, Sil!',
    cancelButtonText: 'İptal'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/v1/admin/skills/${skillId}`);
      
      // Remove from local state
      skills.value = skills.value.filter(s => s.id !== skillId);
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Yetenek başarıyla silindi!',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    } catch (error) {
      console.error('Error deleting skill:', error);
      Swal.fire({
        icon: 'error',
        title: 'Hata!',
        text: 'Yetenek silinirken bir hata oluştu!',
        confirmButtonColor: '#ef4444'
      });
    }
  }
};

// ESC tuşu ile modal kapatma
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && (showCreateModal.value || editingSkill.value)) {
    closeModal();
  }
};

onMounted(async () => {
  await checkAuth();
  await fetchSkills();
  document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyDown);
});
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200;
}

.line-clamp-3 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}
</style>