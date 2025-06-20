<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <button
              @click="router.push('/admin/settings')"
              class="text-gray-600 hover:text-gray-900 font-medium"
            >
              ← Ayarlara Dön
            </button>
            <h1 class="text-2xl font-bold text-gray-900">İş Deneyimi Yönetimi</h1>
          </div>
          <button
            @click="openModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
          >
            <Plus class="w-4 h-4" />
            <span>Yeni Deneyim</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Veriler yükleniyor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="experiences.length === 0" class="text-center py-12">
        <Briefcase class="w-16 h-16 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz deneyim eklenmemiş</h3>
        <p class="text-gray-500 mb-6">İlk iş deneyiminizi eklemek için butona tıklayın.</p>
        <button
          @click="openModal()"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
        >
          İlk Deneyimi Ekle
        </button>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="experience in experiences"
          :key="experience.id"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
        >
          <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900">{{ experience.position }}</h3>
              <p class="text-lg text-blue-600 font-semibold">{{ experience.company_name }}</p>
              <div v-if="experience.location || experience.work_type" class="flex items-center space-x-2 text-gray-500">
                <span v-if="experience.location">{{ experience.location }}</span>
                <span v-if="experience.work_type && experience.location" class="text-gray-400">•</span>
                <span v-if="experience.work_type" class="px-2 py-1 bg-gray-100 rounded-full text-xs font-medium">
                  {{ getWorkTypeLabel(experience.work_type) }}
                </span>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <button
                @click="openModal(experience)"
                class="text-blue-600 hover:text-blue-800 p-2"
              >
                <Edit class="w-4 h-4" />
              </button>
              <button
                @click="deleteExperience(experience.id)"
                class="text-red-600 hover:text-red-800 p-2"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
          
          <div class="flex items-center text-sm text-gray-600 mb-4">
            <Calendar class="w-4 h-4 mr-1" />
            <span>{{ formatDate(experience.start_date) }} - {{ experience.is_current ? 'Devam Ediyor' : formatDate(experience.end_date) }}</span>
            <span class="ml-2 text-gray-400">•</span>
            <span class="ml-2">{{ experience.duration }}</span>
          </div>

          <p v-if="experience.description" class="text-gray-700 mb-4">{{ experience.description }}</p>

          <div v-if="experience.technologies && experience.technologies.length > 0" class="flex flex-wrap gap-2">
            <span
              v-for="tech in experience.technologies"
              :key="tech"
              class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm"
            >
              {{ tech }}
            </span>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ editingExperience ? 'Deneyimi Düzenle' : 'Yeni Deneyim Ekle' }}
          </h3>
        </div>
        
        <form @submit.prevent="saveExperience" class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Pozisyon *</label>
              <input
                v-model="form.position"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Şirket Adı *</label>
              <input
                v-model="form.company_name"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi *</label>
              <input
                v-model="form.start_date"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
              <input
                v-model="form.end_date"
                type="date"
                :disabled="form.is_current"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input
              v-model="form.is_current"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <label class="ml-2 text-sm text-gray-700">Halen burada çalışıyorum</label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Çalışma Türü</label>
              <select
                v-model="form.work_type"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="office">Ofiste</option>
                <option value="remote">Remote</option>
                <option value="hybrid">Hibrit</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Konum</label>
              <input
                v-model="form.location"
                :placeholder="form.work_type === 'remote' ? 'Remote' : 'İstanbul, Türkiye'"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Teknolojiler (virgülle ayırın)</label>
            <input
              v-model="technologiesText"
              placeholder="Laravel, PHP, MySQL, Vue.js"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg disabled:opacity-50"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { Plus, Edit, Trash2, Briefcase, Calendar } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const { checkAuth } = useAuth();

const loading = ref(true);
const saving = ref(false);
const showModal = ref(false);
const experiences = ref([]);
const editingExperience = ref(null);

const form = ref({
  position: '',
  company_name: '',
  description: '',
  start_date: '',
  end_date: '',
  is_current: false,
  location: '',
  work_type: 'office',
  technologies: [],
  sort_order: 0
});

const technologiesText = computed({
  get: () => form.value.technologies?.join(', ') || '',
  set: (value) => {
    form.value.technologies = value ? value.split(',').map(t => t.trim()).filter(t => t) : [];
  }
});

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('tr-TR', { year: 'numeric', month: 'long' });
};

const getWorkTypeLabel = (workType) => {
  const labels = {
    office: 'Ofiste',
    remote: 'Remote',
    hybrid: 'Hibrit'
  };
  return labels[workType] || workType;
};

const fetchExperiences = async () => {
  try {
    const response = await axios.get('/api/experiences');
    experiences.value = response.data;
  } catch (error) {
    console.error('Error fetching experiences:', error);
  } finally {
    loading.value = false;
  }
};

const openModal = (experience = null) => {
  editingExperience.value = experience;
  
  if (experience) {
    form.value = {
      position: experience.position || '',
      company_name: experience.company_name || '',
      description: experience.description || '',
      start_date: experience.start_date || '',
      end_date: experience.end_date || '',
      is_current: experience.is_current || false,
      location: experience.location || '',
      work_type: experience.work_type || 'office',
      technologies: experience.technologies || [],
      sort_order: experience.sort_order || 0
    };
  } else {
    form.value = {
      position: '',
      company_name: '',
      description: '',
      start_date: '',
      end_date: '',
      is_current: false,
      location: '',
      work_type: 'office',
      technologies: [],
      sort_order: experiences.value.length
    };
  }
  
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingExperience.value = null;
};

const saveExperience = async () => {
  saving.value = true;
  try {
    if (editingExperience.value) {
      await axios.put(`/api/experiences/${editingExperience.value.id}`, form.value);
    } else {
      await axios.post('/api/experiences', form.value);
    }
    
    await fetchExperiences();
    closeModal();
    
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Deneyim başarıyla kaydedildi!',
      showConfirmButton: false,
      timer: 3000
    });
  } catch (error) {
    console.error('Error saving experience:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Deneyim kaydedilirken bir hata oluştu!'
    });
  } finally {
    saving.value = false;
  }
};

const deleteExperience = async (id) => {
  const result = await Swal.fire({
    title: 'Emin misiniz?',
    text: 'Bu deneyim kalıcı olarak silinecek!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Evet, Sil!',
    cancelButtonText: 'İptal'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/experiences/${id}`);
      await fetchExperiences();
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Deneyim başarıyla silindi!',
        showConfirmButton: false,
        timer: 3000
      });
    } catch (error) {
      console.error('Error deleting experience:', error);
      Swal.fire({
        icon: 'error',
        title: 'Hata!',
        text: 'Deneyim silinirken bir hata oluştu!'
      });
    }
  }
};

onMounted(async () => {
  await checkAuth();
  await fetchExperiences();
});
</script>