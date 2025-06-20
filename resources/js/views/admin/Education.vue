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
            <h1 class="text-2xl font-bold text-gray-900">Eğitim Yönetimi</h1>
          </div>
          <button
            @click="openModal()"
            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
          >
            <Plus class="w-4 h-4" />
            <span>Yeni Eğitim</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Veriler yükleniyor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="education.length === 0" class="text-center py-12">
        <GraduationCap class="w-16 h-16 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz eğitim eklenmemiş</h3>
        <p class="text-gray-500 mb-6">İlk eğitim bilginizi eklemek için butona tıklayın.</p>
        <button
          @click="openModal()"
          class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg"
        >
          İlk Eğitimi Ekle
        </button>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="edu in education"
          :key="edu.id"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
        >
          <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900">{{ edu.degree }}</h3>
              <p class="text-lg text-purple-600 font-semibold">{{ edu.institution_name }}</p>
              <p v-if="edu.field_of_study" class="text-gray-600">{{ edu.field_of_study }}</p>
              <p v-if="edu.location" class="text-gray-500">{{ edu.location }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <button
                @click="openModal(edu)"
                class="text-purple-600 hover:text-purple-800 p-2"
              >
                <Edit class="w-4 h-4" />
              </button>
              <button
                @click="deleteEducation(edu.id)"
                class="text-red-600 hover:text-red-800 p-2"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
          
          <div class="flex items-center text-sm text-gray-600 mb-4">
            <Calendar class="w-4 h-4 mr-1" />
            <span>{{ edu.start_year }} - {{ edu.is_current ? 'Devam Ediyor' : edu.end_year }}</span>
          </div>

          <p v-if="edu.description" class="text-gray-700">{{ edu.description }}</p>
        </div>
      </div>
    </main>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ editingEducation ? 'Eğitimi Düzenle' : 'Yeni Eğitim Ekle' }}
          </h3>
        </div>
        
        <form @submit.prevent="saveEducation" class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Derece/Diploma *</label>
              <input
                v-model="form.degree"
                required
                placeholder="Lisans, Yüksek Lisans, vb."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kurum Adı *</label>
              <input
                v-model="form.institution_name"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bölüm/Alan</label>
            <input
              v-model="form.field_of_study"
              placeholder="Bilgisayar Programcılığı, Yazılım Mühendisliği, vb."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Aldığınız dersler, projeler, başarılar vb."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Yılı *</label>
              <input
                v-model="form.start_year"
                type="number"
                min="1990"
                max="2030"
                required
                placeholder="2020"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Yılı</label>
              <input
                v-model="form.end_year"
                type="number"
                min="1990"
                max="2030"
                :disabled="form.is_current"
                placeholder="2024"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 disabled:bg-gray-100"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input
              v-model="form.is_current"
              type="checkbox"
              class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
            />
            <label class="ml-2 text-sm text-gray-700">Halen devam ediyorum</label>
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
              class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg disabled:opacity-50"
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus, Edit, Trash2, GraduationCap, Calendar } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const { checkAuth } = useAuth();

const loading = ref(true);
const saving = ref(false);
const showModal = ref(false);
const education = ref([]);
const editingEducation = ref(null);

const form = ref({
  institution_name: '',
  degree: '',
  field_of_study: '',
  start_year: null,
  end_year: null,
  is_current: false,
  sort_order: 0
});

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('tr-TR', { year: 'numeric', month: 'long' });
};

const fetchEducation = async () => {
  try {
    const response = await axios.get('/api/education');
    education.value = response.data;
  } catch (error) {
    console.error('Error fetching education:', error);
  } finally {
    loading.value = false;
  }
};

const openModal = (edu = null) => {
  editingEducation.value = edu;
  
  if (edu) {
    form.value = {
      institution_name: edu.institution_name || '',
      degree: edu.degree || '',
      field_of_study: edu.field_of_study || '',
      start_year: edu.start_year || null,
      end_year: edu.end_year || null,
      is_current: edu.is_current || false,
      sort_order: edu.sort_order || 0
    };
  } else {
    form.value = {
      institution_name: '',
      degree: '',
      field_of_study: '',
      start_year: null,
      end_year: null,
      is_current: false,
      sort_order: education.value.length
    };
  }
  
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingEducation.value = null;
};

const saveEducation = async () => {
  saving.value = true;
  try {
    if (editingEducation.value) {
      await axios.put(`/api/education/${editingEducation.value.id}`, form.value);
    } else {
      await axios.post('/api/education', form.value);
    }
    
    await fetchEducation();
    closeModal();
    
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Eğitim başarıyla kaydedildi!',
      showConfirmButton: false,
      timer: 3000
    });
  } catch (error) {
    console.error('Error saving education:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Eğitim kaydedilirken bir hata oluştu!'
    });
  } finally {
    saving.value = false;
  }
};

const deleteEducation = async (id) => {
  const result = await Swal.fire({
    title: 'Emin misiniz?',
    text: 'Bu eğitim kalıcı olarak silinecek!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Evet, Sil!',
    cancelButtonText: 'İptal'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/education/${id}`);
      await fetchEducation();
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Eğitim başarıyla silindi!',
        showConfirmButton: false,
        timer: 3000
      });
    } catch (error) {
      console.error('Error deleting education:', error);
      Swal.fire({
        icon: 'error',
        title: 'Hata!',
        text: 'Eğitim silinirken bir hata oluştu!'
      });
    }
  }
};

onMounted(async () => {
  await checkAuth();
  await fetchEducation();
});
</script>