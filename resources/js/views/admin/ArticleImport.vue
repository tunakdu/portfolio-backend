<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <button
              @click="router.push('/admin/articles')"
              class="text-gray-600 hover:text-gray-900 font-medium"
            >
              ← Makaleler
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Medium'dan İçe Aktar</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Import Methods -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- URL Import -->
        <div class="modern-card p-6">
          <div class="flex items-center mb-4">
            <Link class="w-6 h-6 text-blue-600 mr-3" />
            <h3 class="text-lg font-semibold text-gray-900">URL ile İçe Aktar</h3>
          </div>
          <p class="text-gray-600 mb-4">Medium makale URL'sini girerek tek makale aktarın</p>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Medium Makale URL'si
              </label>
              <input
                v-model="importUrl"
                type="url"
                placeholder="https://medium.com/@username/article-title"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
              />
            </div>
            <button
              @click="importFromUrl"
              :disabled="isImporting || !importUrl"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div v-if="isImporting" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                İçe Aktarılıyor...
              </div>
              <span v-else>URL'den İçe Aktar</span>
            </button>
          </div>
        </div>

        <!-- File Upload -->
        <div class="modern-card p-6">
          <div class="flex items-center mb-4">
            <Upload class="w-6 h-6 text-green-600 mr-3" />
            <h3 class="text-lg font-semibold text-gray-900">Dosya Yükleme</h3>
          </div>
          <p class="text-gray-600 mb-4">HTML veya Markdown dosyası yükleyerek içe aktarın</p>
          
          <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
            <input
              type="file"
              ref="fileInput"
              @change="handleFileUpload"
              accept=".html,.md,.txt"
              class="hidden"
            />
            <Upload class="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <h4 class="text-md font-medium text-gray-900 mb-2">Dosya Yükle</h4>
            <p class="text-gray-500 mb-4">HTML, Markdown veya TXT formatında</p>
            <button
              @click="$refs.fileInput.click()"
              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700"
            >
              Dosya Seç
            </button>
          </div>
        </div>
      </div>

      <!-- Manual Input -->
      <div class="modern-card p-6 mb-8">
        <div class="flex items-center mb-4">
          <Edit class="w-6 h-6 text-purple-600 mr-3" />
          <h3 class="text-lg font-semibold text-gray-900">Manuel Metin Girişi</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Başlık
            </label>
            <input
              v-model="manualArticle.title"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
              placeholder="Makale başlığı..."
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Kategori
            </label>
            <select
              v-model="manualArticle.category_id"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
            >
              <option value="">Kategori Seçin</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            İçerik (HTML veya Markdown)
          </label>
          <textarea
            v-model="manualArticle.content"
            rows="15"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none font-mono text-sm"
            placeholder="Makale içeriğini buraya yapıştırın..."
          ></textarea>
        </div>

        <div class="mt-6 flex justify-end">
          <button
            @click="saveManualArticle"
            :disabled="!canSaveManual"
            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Makaleyi Kaydet
          </button>
        </div>
      </div>

      <!-- Import History -->
      <div v-if="importHistory.length > 0" class="modern-card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">İçe Aktarma Geçmişi</h3>
        <div class="space-y-3">
          <div
            v-for="item in importHistory"
            :key="item.id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div>
              <p class="font-medium text-gray-900">{{ item.title }}</p>
              <p class="text-sm text-gray-500">{{ formatDate(item.created_at) }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <span :class="getStatusClass(item.status)">
                {{ getStatusText(item.status) }}
              </span>
              <button
                @click="viewArticle(item.id)"
                class="text-blue-600 hover:text-blue-700 text-sm font-medium"
              >
                Görüntüle
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Link, Upload, Edit } from 'lucide-vue-next'
import axios from 'axios'
import Swal from 'sweetalert2'

const router = useRouter()

const importUrl = ref('')
const isImporting = ref(false)
const importHistory = ref([])
const categories = ref([])

const manualArticle = ref({
  title: '',
  content: '',
  category_id: ''
})

const canSaveManual = computed(() => {
  return manualArticle.value.title && manualArticle.value.content
})

const importFromUrl = async () => {
  if (!importUrl.value) return
  
  isImporting.value = true
  try {
    const response = await axios.post('/api/articles/import-medium', {
      url: importUrl.value
    })
    
    Swal.fire({
      icon: 'success',
      title: 'Başarılı!',
      text: 'Makale başarıyla içe aktarıldı!',
      confirmButtonColor: '#10b981'
    })
    
    importUrl.value = ''
    loadImportHistory()
    
  } catch (error) {
    console.error('Import error:', error)
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: error.response?.data?.message || 'İçe aktarma sırasında hata oluştu!',
      confirmButtonColor: '#ef4444'
    })
  } finally {
    isImporting.value = false
  }
}

const handleFileUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  const formData = new FormData()
  formData.append('file', file)
  
  try {
    const response = await axios.post('/api/articles/import-file', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    Swal.fire({
      icon: 'success',
      title: 'Başarılı!',
      text: 'Dosya başarıyla içe aktarıldı!',
      confirmButtonColor: '#10b981'
    })
    
    loadImportHistory()
    
  } catch (error) {
    console.error('File import error:', error)
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Dosya içe aktarılırken hata oluştu!',
      confirmButtonColor: '#ef4444'
    })
  }
  
  event.target.value = ''
}

const saveManualArticle = async () => {
  try {
    const response = await axios.post('/api/articles', {
      title: manualArticle.value.title,
      content: manualArticle.value.content,
      category_id: manualArticle.value.category_id,
      status: 'published',
      excerpt: manualArticle.value.content.substring(0, 200) + '...'
    })
    
    Swal.fire({
      icon: 'success',
      title: 'Başarılı!',
      text: 'Makale başarıyla kaydedildi!',
      confirmButtonColor: '#10b981'
    })
    
    manualArticle.value = {
      title: '',
      content: '',
      category_id: ''
    }
    
    loadImportHistory()
    
  } catch (error) {
    console.error('Save error:', error)
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Makale kaydedilirken hata oluştu!',
      confirmButtonColor: '#ef4444'
    })
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get('/api/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Categories load error:', error)
  }
}

const loadImportHistory = async () => {
  try {
    const response = await axios.get('/api/articles')
    importHistory.value = response.data.slice(0, 5) // Son 5 makale
  } catch (error) {
    console.error('History load error:', error)
  }
}

const viewArticle = (id) => {
  router.push(`/admin/articles/${id}`)
}

const getStatusClass = (status) => {
  return {
    'published': 'text-green-700 bg-green-100 px-2 py-1 rounded text-xs',
    'draft': 'text-yellow-700 bg-yellow-100 px-2 py-1 rounded text-xs',
    'archived': 'text-gray-700 bg-gray-100 px-2 py-1 rounded text-xs'
  }[status] || 'text-gray-700 bg-gray-100 px-2 py-1 rounded text-xs'
}

const getStatusText = (status) => {
  return {
    'published': 'Yayınlandı',
    'draft': 'Taslak',
    'archived': 'Arşivlendi'
  }[status] || 'Bilinmiyor'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadCategories()
  loadImportHistory()
})
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200;
}
</style>