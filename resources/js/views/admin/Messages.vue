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
            <h1 class="text-2xl font-bold text-gray-900">Mesajlar</h1>
            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
              {{ unreadCount }} Okunmamış
            </span>
          </div>
          <div class="flex items-center space-x-3">
            <button
              @click="refreshMessages"
              :disabled="isLoading"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <RefreshCw :class="['w-4 h-4 mr-2', isLoading && 'animate-spin']" />
              {{ isLoading ? 'Yükleniyor...' : 'Yenile' }}
            </button>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-6 gap-6 h-[calc(100vh-12rem)]">
        <!-- Messages List - Sol Taraf -->
        <div class="flex flex-col lg:col-span-2">
          <!-- Filters -->
          <div class="modern-card p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1 relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                <input
                  type="text"
                  placeholder="Mesajlarda ara..."
                  v-model="searchTerm"
                  class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                />
              </div>
              <div class="flex items-center space-x-2">
                <Filter class="w-4 h-4 text-gray-400" />
                <select
                  v-model="statusFilter"
                  class="px-3 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                >
                  <option value="">Tümü</option>
                  <option value="unread">Okunmamış</option>
                  <option value="read">Okunmuş</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Messages List -->
          <div class="flex-1 modern-card">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">İletişim Mesajları</h3>
            </div>
            <div class="overflow-auto h-full">
              <div v-if="isLoading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
              </div>
              <div v-else-if="filteredMessages.length === 0" class="p-8 text-center text-gray-500">
                <Inbox class="w-12 h-12 mx-auto mb-4 text-gray-300" />
                <p>Mesaj bulunamadı</p>
              </div>
              <div v-else class="divide-y divide-gray-200">
                <div
                  v-for="message in filteredMessages"
                  :key="message.id"
                  @click="selectMessage(message)"
                  :class="[
                    'p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-200',
                    selectedMessage?.id === message.id ? 'bg-blue-50 border-r-2 border-blue-500' : '',
                    !message.is_read ? 'bg-blue-25' : ''
                  ]"
                >
                  <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                      <div class="flex items-center justify-between">
                        <p :class="[
                          'text-sm font-medium truncate',
                          !message.is_read ? 'text-gray-900' : 'text-gray-600'
                        ]">
                          {{ message.name }}
                        </p>
                        <div class="flex items-center space-x-1">
                          <div v-if="!message.is_read" class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        </div>
                      </div>
                      <p class="text-xs text-gray-500 mb-1">{{ message.email }}</p>
                      <p class="text-sm text-gray-600 truncate">{{ message.subject }}</p>
                      <p class="text-xs text-gray-400 mt-1">{{ formatDate(message.created_at) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Message Detail - Sağ Taraf -->
        <div class="lg:col-span-4">
          <div v-if="!selectedMessage" class="modern-card h-full flex items-center justify-center">
            <div class="text-center text-gray-500">
              <Mail class="w-16 h-16 mx-auto mb-4 text-gray-300" />
              <p class="text-lg font-medium">Mesaj Seçin</p>
              <p class="text-sm">Detayları görüntülemek için soldaki listeden bir mesaj seçin</p>
            </div>
          </div>
          <div v-else class="modern-card h-full flex flex-col">
            <!-- Message Header -->
            <div class="p-6 border-b border-gray-200">
              <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                  <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ selectedMessage.subject }}</h2>
                  <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                      <User class="w-4 h-4" />
                      <span>{{ selectedMessage.name }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <Mail class="w-4 h-4" />
                      <span>{{ selectedMessage.email }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <Clock class="w-4 h-4" />
                      <span>{{ formatDate(selectedMessage.created_at) }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button
                    @click="toggleMessageStatus(selectedMessage)"
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200',
                      selectedMessage.is_read 
                        ? 'bg-green-100 text-green-800 hover:bg-green-200'
                        : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                    ]"
                  >
                    {{ selectedMessage.is_read ? 'Okundu' : 'Okunmadı' }}
                  </button>
                  <button
                    @click="deleteMessage(selectedMessage)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Message Content -->
            <div class="flex-1 p-6 overflow-auto">
              <div class="prose max-w-none">
                <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">{{ selectedMessage.message }}</div>
              </div>
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
import axios from 'axios'
import { 
  Search, 
  Filter, 
  RefreshCw, 
  Mail, 
  User, 
  Clock, 
  Trash2, 
  Inbox 
} from 'lucide-vue-next'

const router = useRouter()

// Reactive Data
const messages = ref([])
const selectedMessage = ref(null)
const searchTerm = ref('')
const statusFilter = ref('')
const isLoading = ref(false)

// Computed
const unreadCount = computed(() => 
  messages.value.filter(msg => !msg.is_read).length
)

const filteredMessages = computed(() => {
  let filtered = messages.value

  if (searchTerm.value) {
    const search = searchTerm.value.toLowerCase()
    filtered = filtered.filter(msg => 
      msg.name.toLowerCase().includes(search) ||
      msg.email.toLowerCase().includes(search) ||
      msg.subject.toLowerCase().includes(search) ||
      msg.message.toLowerCase().includes(search)
    )
  }

  if (statusFilter.value) {
    if (statusFilter.value === 'unread') {
      filtered = filtered.filter(msg => !msg.is_read)
    } else if (statusFilter.value === 'read') {
      filtered = filtered.filter(msg => msg.is_read)
    }
  }

  return filtered.sort((a, b) => {
    // Önce message_date'e göre sırala, yoksa created_at kullan
    const dateA = new Date(a.message_date || a.created_at)
    const dateB = new Date(b.message_date || b.created_at)
    return dateB - dateA // En yeni önce
  })
})

// Methods
const fetchMessages = async () => {
  try {
    isLoading.value = true
    const response = await axios.get('/api/messages')
    messages.value = response.data.data
  } catch (error) {
    console.error('Mesajlar yüklenirken hata:', error)
  } finally {
    isLoading.value = false
  }
}

const refreshMessages = () => {
  fetchMessages()
}

const selectMessage = async (message) => {
  selectedMessage.value = message
  
  if (!message.is_read) {
    try {
      await axios.put(`/api/messages/${message.id}`, {
        is_read: true
      })
      message.is_read = true
    } catch (error) {
      console.error('Mesaj okundu olarak işaretlenirken hata:', error)
    }
  }
}

const toggleMessageStatus = async (message) => {
  try {
    const newStatus = !message.is_read
    await axios.put(`/api/messages/${message.id}`, {
      is_read: newStatus
    })
    message.is_read = newStatus
  } catch (error) {
    console.error('Mesaj durumu güncellenirken hata:', error)
  }
}

const deleteMessage = async (message) => {
  if (!confirm('Bu mesajı silmek istediğinizden emin misiniz?')) {
    return
  }

  try {
    await axios.delete(`/api/messages/${message.id}`)
    messages.value = messages.value.filter(m => m.id !== message.id)
    
    if (selectedMessage.value?.id === message.id) {
      selectedMessage.value = null
    }
  } catch (error) {
    console.error('Mesaj silinirken hata:', error)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Lifecycle
onMounted(() => {
  fetchMessages()
})
</script>

<style scoped>
.modern-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.bg-blue-25 {
  background-color: rgba(59, 130, 246, 0.05);
}

.prose {
  max-width: none;
}
</style>