<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <button
              @click="router.push('/admin/projects')"
              class="text-gray-600 hover:text-gray-900 font-medium"
            >
              ← Projeler
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Yeni Proje</h1>
          </div>
          <div class="flex items-center space-x-3">
            <button
              @click="router.push('/admin/projects')"
              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700"
            >
              İptal
            </button>
            <button
              @click="handleSubmit"
              :disabled="isSaving"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 disabled:opacity-50"
            >
              <Save class="w-4 h-4" />
              <span>{{ isSaving ? 'Kaydediliyor...' : 'Projeyi Kaydet' }}</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="handleSubmit" class="space-y-8">
        <!-- Basic Information -->
        <div class="modern-card p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Temel Bilgiler</h2>
          
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Proje Başlığı *
              </label>
              <input
                v-model="formData.title"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                placeholder="Proje başlığını giriniz"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Açıklama *
              </label>
              <textarea
                v-model="formData.description"
                rows="4"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"
                placeholder="Proje hakkında detaylı açıklama yazınız"
                required
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Kategori *
                </label>
                <select
                  v-model="formData.category"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  required
                >
                  <option value="">Kategori seçiniz</option>
                  <option value="Web Development">Web Development</option>
                  <option value="API Development">API Development</option>
                  <option value="Mobile App">Mobile App</option>
                  <option value="Desktop App">Desktop App</option>
                  <option value="E-Commerce">E-Commerce</option>
                  <option value="SaaS">SaaS</option>
                  <option value="Other">Diğer</option>
                </select>
              </div>

              <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer">
                  <input
                    v-model="formData.featured"
                    type="checkbox"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  />
                  <span class="text-sm font-medium text-gray-700">Öne çıkan proje</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Technologies -->
        <div class="modern-card p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Teknolojiler</h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Kullanılan Teknolojiler
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
          </div>
        </div>

        <!-- Media & Links -->
        <div class="modern-card p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Medya ve Bağlantılar</h2>
          
          <div class="space-y-6">
            <!-- Image Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Proje Görseli
              </label>
              
              <!-- Image Preview -->
              <div v-if="formData.image" class="mb-4">
                <img 
                  :src="formData.image" 
                  alt="Project preview" 
                  class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200"
                />
              </div>
              
              <!-- Upload Area -->
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                <Upload class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <h3 class="text-md font-medium text-gray-900 mb-2">Proje Görseli Yükle</h3>
                <p class="text-gray-500 mb-4">JPG, PNG veya WebP formatında görsel yükleyin</p>
                
                <input
                  type="file"
                  accept="image/*"
                  @change="handleImageUpload"
                  :disabled="isUploading"
                  class="hidden"
                  id="project-image-upload"
                />
                
                <label
                  for="project-image-upload"
                  :class="`inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer ${
                    isUploading ? 'opacity-50 cursor-not-allowed' : ''
                  }`"
                >
                  <div v-if="isUploading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                  <Upload v-else class="w-4 h-4 mr-2" />
                  {{ isUploading ? 'Yükleniyor...' : 'Görsel Seç' }}
                </label>
                
                <p class="text-xs text-gray-500 mt-2">Maksimum 5MB</p>
              </div>
              
              <!-- Manual URL Input -->
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Veya URL ile ekle
                </label>
                <input
                  v-model="formData.image"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  placeholder="https://example.com/image.jpg"
                />
              </div>
            </div>

            <!-- Project Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <ExternalLink class="w-4 h-4 inline mr-1" />
                  Canlı URL
                </label>
                <input
                  v-model="formData.live_url"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  placeholder="https://project-demo.com"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <Github class="w-4 h-4 inline mr-1" />
                  GitHub URL
                </label>
                <input
                  v-model="formData.github_url"
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  placeholder="https://github.com/username/project"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Details -->
        <div class="modern-card p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Ek Detaylar</h2>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Detaylı Açıklama
            </label>
            <textarea
              v-model="formData.content"
              rows="8"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"
              placeholder="Proje hakkında daha detaylı bilgi, kullanılan teknolojiler, karşılaşılan zorluklar, çözümler vb..."
            />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="router.push('/admin/projects')"
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
            <span>{{ isSaving ? 'Kaydediliyor...' : 'Projeyi Kaydet' }}</span>
          </button>
        </div>
      </form>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Save, Upload, Plus, X, ExternalLink, Github } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const { checkAuth } = useAuth();

const isSaving = ref(false);
const isUploading = ref(false);
const newTechnology = ref('');

const formData = ref({
  title: '',
  description: '',
  category: '',
  technologies: [],
  image: '',
  live_url: '',
  github_url: '',
  content: '',
  featured: false
});

const addTechnology = () => {
  if (newTechnology.value.trim() && !formData.value.technologies.includes(newTechnology.value.trim())) {
    formData.value.technologies.push(newTechnology.value.trim());
    newTechnology.value = '';
  }
};

const removeTechnology = (index) => {
  formData.value.technologies.splice(index, 1);
};

const handleImageUpload = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  isUploading.value = true;
  try {
    const formDataUpload = new FormData();
    formDataUpload.append('file', file);
    formDataUpload.append('type', 'project');

    const response = await axios.post('/api/upload', formDataUpload);
    
    if (response.data.url) {
      formData.value.image = response.data.url;
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Görsel başarıyla yüklendi!',
        showConfirmButton: false,
        timer: 3000,
      });
    }
  } catch (error) {
    console.error('Upload error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Görsel yüklenirken hata oluştu!',
    });
  } finally {
    isUploading.value = false;
  }
};

const handleSubmit = async () => {
  if (!formData.value.title || !formData.value.description || !formData.value.category) {
    Swal.fire({
      icon: 'warning',
      title: 'Eksik Bilgi!',
      text: 'Lütfen zorunlu alanları doldurun.',
    });
    return;
  }

  isSaving.value = true;
  try {
    const projectData = {
      ...formData.value,
      technologies: JSON.stringify(formData.value.technologies)
    };

    await axios.post('/api/projects', projectData);
    
    Swal.fire({
      icon: 'success',
      title: 'Başarılı!',
      text: 'Proje başarıyla kaydedildi!',
      confirmButtonColor: '#2563eb'
    }).then(() => {
      router.push('/admin/projects');
    });
  } catch (error) {
    console.error('Error saving project:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Proje kaydedilirken bir hata oluştu!',
      confirmButtonColor: '#ef4444'
    });
  } finally {
    isSaving.value = false;
  }
};

onMounted(async () => {
  await checkAuth();
});
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200;
}
</style>