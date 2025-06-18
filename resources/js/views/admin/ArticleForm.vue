<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <button
              @click="$router.back()"
              class="text-gray-600 hover:text-gray-900 font-medium"
            >
              ← Geri
            </button>
            <h1 class="text-2xl font-bold text-gray-900">{{ isEditing ? 'Makale Düzenle' : 'Yeni Makale Ekle' }}</h1>
          </div>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="isLoading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">{{ isEditing ? 'Makale yükleniyor...' : 'Kayıt ediliyor...' }}</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-32">
      <form @submit.prevent="saveArticle" class="modern-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
              Başlık <span class="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              v-model="form.title" 
              id="title" 
              class="w-full px-3 py-2 border rounded-lg transition-colors" 
              :class="errors.title ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
              placeholder="Makale başlığını girin..."
              required
            >
            <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title[0] }}</p>
          </div>
          <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
              URL Slug
              <span class="text-gray-400 text-xs">(Otomatik oluşturulur)</span>
            </label>
            <input 
              type="text" 
              v-model="form.slug" 
              id="slug" 
              class="w-full px-3 py-2 border rounded-lg transition-colors" 
              :class="errors.slug ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
              placeholder="url-slug-ornek"
            >
            <p v-if="errors.slug" class="text-red-500 text-sm mt-1">{{ errors.slug[0] }}</p>
            <p class="text-xs text-gray-500 mt-1">Boş bırakılırsa başlıktan otomatik oluşturulur</p>
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Kapak Görseli</label>
          
          <!-- Dosya Yükleme Alanı -->
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
            <div v-if="!form.cover_image" class="space-y-4">
              <div class="mx-auto w-16 h-16 text-gray-400">
                <svg fill="none" stroke="currentColor" viewBox="0 0 48 48" aria-hidden="true">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
              <div>
                <label for="cover_image_file" class="cursor-pointer">
                  <span class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Görsel Yükle
                  </span>
                  <input 
                    id="cover_image_file" 
                    type="file" 
                    class="sr-only" 
                    accept="image/*"
                    @change="handleImageUpload"
                    :disabled="imageUploading"
                  >
                </label>
                <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF, WEBP desteklenir (Maks. 2MB)</p>
              </div>
            </div>
            
            <!-- Yükleme Durumu -->
            <div v-if="imageUploading" class="space-y-4">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p class="text-sm text-gray-600">Görsel yükleniyor...</p>
            </div>
            
            <!-- Yüklenmiş Görsel Önizlemesi -->
            <div v-if="form.cover_image && !imageUploading" class="space-y-4">
              <img :src="form.cover_image" alt="Kapak görseli önizlemesi" class="max-w-full max-h-48 mx-auto rounded-lg shadow-sm object-cover">
              <div class="flex items-center justify-center space-x-3">
                <label for="cover_image_file" class="cursor-pointer inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                  </svg>
                  Değiştir
                  <input 
                    id="cover_image_file" 
                    type="file" 
                    class="sr-only" 
                    accept="image/*"
                    @change="handleImageUpload"
                    :disabled="imageUploading"
                  >
                </label>
                <button 
                  type="button" 
                  @click="removeCoverImage" 
                  class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 transition-colors"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Kaldır
                </button>
              </div>
            </div>
          </div>
          <p v-if="errors.cover_image" class="text-red-500 text-sm mt-1">{{ errors.cover_image[0] }}</p>
        </div>

        <div class="mb-6">
          <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
            İçerik <span class="text-red-500">*</span>
          </label>
          <div class="border rounded-lg" :class="errors.content ? 'border-red-300' : 'border-gray-300'">
            <Editor id="content" v-model="form.content" />
          </div>
          <p v-if="errors.content" class="text-red-500 text-sm mt-1">{{ errors.content[0] }}</p>
          <p class="text-xs text-gray-500 mt-1">Makale içeriğini yazın. HTML etiketleri desteklenir.</p>
        </div>

        <!-- Yayın Ayarları -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-gray-700 mb-3">Yayın Ayarları</h3>
          <div class="space-y-3">
            <label class="inline-flex items-center">
              <input 
                type="checkbox" 
                v-model="form.is_published" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <span class="ml-2 text-sm font-medium text-gray-700">Hemen yayınla</span>
            </label>
            <p class="text-xs text-gray-500">
              Bu seçenek işaretliyse makale kaydedildikten sonra hemen yayına alınır.
            </p>
          </div>
        </div>

        <!-- Sticky Bottom Actions -->
        <div class="sticky bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 mt-8 -mx-6 z-50">
          <div class="flex items-center justify-between max-w-4xl mx-auto">
            <div class="text-sm text-gray-500">
              <span v-if="isEditing">Son düzenleme: {{ formatDate(article.updated_at) }}</span>
              <span v-else>Yeni makale oluşturuluyor</span>
            </div>
            <div class="flex items-center space-x-3">
              <button 
                type="button" 
                @click="$router.back()" 
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm"
                :disabled="isLoading"
              >
                İptal
              </button>
              <button 
                type="button" 
                @click="saveDraft" 
                v-if="!isEditing"
                :disabled="isLoading || !form.title || !form.content"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors disabled:opacity-50 shadow-sm"
              >
                Taslak Kaydet
              </button>
              <button 
                type="submit" 
                :disabled="isLoading || !form.title || !form.content"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 flex items-center shadow-sm"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoading ? 'Kaydediliyor...' : (isEditing ? 'Güncelle' : 'Kaydet') }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import useArticles from "../../composables/useArticles";
import Editor from "../../components/Editor.vue";
import { computed } from "vue";
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

const { article, getArticle, storeArticle, updateArticle, uploadCoverImage, errors, isLoading } = useArticles();

const form = ref({
    title: '',
    slug: '',
    content: '',
    cover_image: '',
    is_published: false
});

const imageError = ref(false);
const imageUploading = ref(false);

const isEditing = computed(() => !!route.params.slug);

const generateSlug = (title) => {
    return title
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
};

watch(() => form.value.title, (newTitle) => {
    if (!isEditing.value) {
        form.value.slug = generateSlug(newTitle);
    }
});

onMounted(async () => {
    if (isEditing.value) {
        await getArticle(route.params.slug);
        form.value = { ...article.value };
    }
});

const saveArticle = async () => {
    if (isEditing.value) {
        await updateArticle(route.params.slug, form.value);
    } else {
        await storeArticle(form.value);
    }
};

const saveDraft = async () => {
    const draftData = { ...form.value, is_published: false };
    await storeArticle(draftData);
};

const handleImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    // Dosya tipi kontrolü
    if (!file.type.startsWith('image/')) {
        Swal.fire('Hata!', 'Lütfen sadece resim dosyaları seçin.', 'error');
        return;
    }
    
    // Dosya boyutu kontrolü (2MB)
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire('Hata!', 'Dosya boyutu 2MB\'dan küçük olmalıdır.', 'error');
        return;
    }
    
    try {
        imageUploading.value = true;
        const imagePath = await uploadCoverImage(file);
        form.value.cover_image = imagePath;
        // Inputı temizle
        event.target.value = '';
    } catch (error) {
        console.error('Görsel yükleme hatası:', error);
    } finally {
        imageUploading.value = false;
    }
};

const removeCoverImage = () => {
    form.value.cover_image = '';
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200;
}
</style>
