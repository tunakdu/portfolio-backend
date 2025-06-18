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
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="isLoading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Mesajlar yükleniyor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Tabs -->
      <div class="modern-card p-1 mb-6">
        <div class="flex space-x-1">
          <button
            @click="activeTab = 'CONTACT_FORM'"
            :class="`flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
              activeTab === 'CONTACT_FORM'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-600 hover:text-gray-900'
            }`"
          >
            📝 İletişim Formu ({{ contactMessages.length }})
          </button>
          <button
            @click="activeTab = 'GMAIL'"
            :class="`flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
              activeTab === 'GMAIL'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-600 hover:text-gray-900'
            }`"
          >
            📧 Gmail ({{ gmailMessages.length }})
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-[calc(100vh-12rem)]">
        <!-- Messages List - Sol Taraf -->
        <div class="flex flex-col">
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
                  <option value="ALL">Tüm Durumlar</option>
                  <option value="UNREAD">Okunmadı</option>
                  <option value="READ">Okundu</option>
                  <option value="REPLIED">Yanıtlandı</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Messages -->
          <div class="flex-1 overflow-y-auto space-y-4 pr-2">
            <div v-if="filteredMessages.length === 0" class="modern-card p-8 text-center">
              <Mail class="w-12 h-12 text-gray-400 mx-auto mb-4" />
              <h3 class="text-lg font-medium text-gray-900 mb-2">Mesaj bulunamadı</h3>
              <p class="text-gray-500">Arama kriterlerinize uygun mesaj bulunmuyor.</p>
            </div>
            
            <div
              v-else
              v-for="message in filteredMessages"
              :key="message.id"
              @click="selectedMessage = message"
              :class="`modern-card p-4 cursor-pointer transition-all duration-200 hover:shadow-md ${
                selectedMessage?.id === message.id ? 'ring-2 ring-blue-500' : ''
              } ${message.status === 'UNREAD' ? 'bg-blue-50 border-l-4 border-blue-500' : ''}`"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-3 mb-2">
                    <h3 class="text-sm font-semibold text-gray-900 truncate">
                      {{ message.name }}
                    </h3>
                    <span :class="`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getStatusConfig(message.status).color}`">
                      <component :is="getStatusConfig(message.status).icon" class="w-3 h-3 mr-1" />
                      {{ getStatusConfig(message.status).label }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 mb-1">{{ message.email }}</p>
                  <p class="text-sm font-medium text-gray-900 mb-2 truncate">
                    {{ message.subject }}
                  </p>
                  <p class="text-sm text-gray-600 line-clamp-2">
                    {{ message.message }}
                  </p>
                </div>
                <div class="ml-4 text-xs text-gray-500">
                  <Clock class="w-3 h-3 inline mr-1" />
                  {{ formatDate(message.createdAt) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Message Preview - Sağ Taraf -->
        <div class="flex flex-col">
          <div v-if="selectedMessage">
            <!-- Action Bar -->
            <div class="modern-card p-4 mb-4">
              <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center space-x-2">
                  <label class="text-sm font-medium text-gray-700">Durum:</label>
                  <select
                    :value="selectedMessage.status"
                    @change="handleStatusChange(selectedMessage.id, $event.target.value)"
                    class="px-3 py-1 border border-gray-300 rounded text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 outline-none"
                  >
                    <option value="UNREAD">Okunmadı</option>
                    <option value="read">Okundu</option>
                    <option v-if="activeTab === 'CONTACT_FORM'" value="REPLIED">Yanıtlandı</option>
                  </select>
                </div>
                
                <div class="flex space-x-2 ml-auto">
                  <button
                    @click="handleReply"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center space-x-1"
                  >
                    <Mail class="w-4 h-4" />
                    <span>Yanıtla</span>
                  </button>
                  <button
                    @click="handleDelete(selectedMessage.id)"
                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg flex items-center space-x-1"
                  >
                    <XCircle class="w-4 h-4" />
                    <span>Sil</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Message Content -->
            <div class="modern-card flex-1 overflow-hidden flex flex-col">
              <!-- Header -->
              <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ selectedMessage.subject }}</h2>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                  <div class="flex items-center space-x-2">
                    <span class="font-medium">Gönderen:</span>
                    <span>{{ selectedMessage.name }}</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span class="font-medium">Email:</span>
                    <span>{{ selectedMessage.email }}</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span class="font-medium">Tarih:</span>
                    <span>{{ formatDate(selectedMessage.createdAt) }}</span>
                  </div>
                </div>
              </div>

              <!-- Content -->
              <div class="p-6 flex-1 overflow-y-auto">
                <div class="prose max-w-none">
                  <p class="whitespace-pre-wrap text-gray-800 leading-relaxed">
                    {{ selectedMessage.message }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="modern-card flex-1 flex items-center justify-center">
            <div class="text-center">
              <Mail class="w-16 h-16 text-gray-400 mx-auto mb-4" />
              <h3 class="text-lg font-medium text-gray-900 mb-2">Mesaj Seçin</h3>
              <p class="text-gray-500">Sol taraftan bir mesaj seçerek detaylarını görüntüleyin.</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Mail, MailOpen, Clock, CheckCircle, XCircle, Search, Filter } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const { checkAuth } = useAuth();

const contactMessages = ref([]);
const gmailMessages = ref([]);
const isLoading = ref(true);
const searchTerm = ref('');
const statusFilter = ref('ALL');
const selectedMessage = ref(null);
const activeTab = ref('CONTACT_FORM');

const statusConfig = {
  UNREAD: { label: 'Okunmadı', color: 'bg-red-100 text-red-800', icon: Mail },
  read: { label: 'Okundu', color: 'bg-yellow-100 text-yellow-800', icon: MailOpen },
  READ: { label: 'Okundu', color: 'bg-yellow-100 text-yellow-800', icon: MailOpen },
  REPLIED: { label: 'Yanıtlandı', color: 'bg-green-100 text-green-800', icon: CheckCircle }
};

const getStatusConfig = (status) => statusConfig[status] || { label: 'Bilinmiyor', color: 'bg-gray-100 text-gray-800', icon: Mail };

const currentMessages = computed(() => {
  return activeTab.value === 'CONTACT_FORM' ? contactMessages.value : gmailMessages.value;
});

const unreadCount = computed(() => {
  return currentMessages.value.filter(m => m.status === 'UNREAD').length;
});

const filteredMessages = computed(() => {
  return currentMessages.value.filter(message => {
    const matchesSearch = message.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
                         message.email.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
                         message.subject.toLowerCase().includes(searchTerm.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'ALL' || message.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const fetchMessages = async () => {
  try {
    const response = await axios.get('/api/messages');
    contactMessages.value = response.data.map(msg => ({
      ...msg,
      source: 'CONTACT_FORM'
    }));
  } catch (error) {
    console.error('Contact form messages fetch error:', error);
    contactMessages.value = [];
  }
};

const fetchGmailMessages = async () => {
  try {
    // Mock data for Gmail messages since API might not be implemented
    const mockGmailMessages = [
      {
        id: 'gmail-1',
        name: 'John Doe',
        email: 'john@example.com',
        subject: 'Project Inquiry',
        message: 'I am interested in collaborating on a project...',
        status: 'UNREAD',
        createdAt: new Date().toISOString(),
        source: 'GMAIL'
      },
      {
        id: 'gmail-2',
        name: 'Jane Smith',
        email: 'jane@company.com',
        subject: 'Business Proposal',
        message: 'We have a business proposal that might interest you...',
        status: 'read',
        createdAt: new Date(Date.now() - 86400000).toISOString(),
        source: 'GMAIL'
      }
    ];
    gmailMessages.value = mockGmailMessages;
  } catch (error) {
    console.error('Gmail messages fetch error:', error);
    gmailMessages.value = [];
  } finally {
    isLoading.value = false;
  }
};

const handleStatusChange = async (messageId, newStatus) => {
  try {
    if (activeTab.value === 'CONTACT_FORM') {
      await axios.patch(`/api/messages/${messageId}`, { status: newStatus });
      
      contactMessages.value = contactMessages.value.map(msg =>
        msg.id === messageId ? { ...msg, status: newStatus } : msg
      );
      
      if (selectedMessage.value && selectedMessage.value.id === messageId) {
        selectedMessage.value = { ...selectedMessage.value, status: newStatus };
      }
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Mesaj durumu güncellendi!',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    }
  } catch (error) {
    console.error('Status update error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Mesaj durumu güncellenemedi'
    });
  }
};

const handleReply = async () => {
  if (!selectedMessage.value) return;
  
  const result = await Swal.fire({
    title: 'Email Yanıtla',
    text: `${selectedMessage.value.email} adresine yanıt göndermek istiyor musunuz?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2563eb',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Evet, Yanıtla',
    cancelButtonText: 'İptal'
  });
  
  if (result.isConfirmed) {
    window.open(`mailto:${selectedMessage.value.email}?subject=Re: ${selectedMessage.value.subject}`);
    
    await Swal.fire({
      icon: 'success',
      title: 'Email Açıldı!',
      text: 'Email istemciniz açıldı. Yanıtınızı yazabilirsiniz.',
      timer: 2000,
      showConfirmButton: false
    });
  }
};

const handleDelete = async (messageId) => {
  const result = await Swal.fire({
    title: 'Mesajı Sil?',
    text: 'Bu mesajı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Evet, Sil',
    cancelButtonText: 'İptal'
  });

  if (result.isConfirmed) {
    try {
      if (activeTab.value === 'CONTACT_FORM') {
        await axios.delete(`/api/messages/${messageId}`);
        
        contactMessages.value = contactMessages.value.filter(msg => msg.id !== messageId);
        if (selectedMessage.value?.id === messageId) {
          selectedMessage.value = null;
        }
        
        await Swal.fire({
          icon: 'success',
          title: 'Silindi!',
          text: 'Mesaj başarıyla silindi',
          timer: 1500,
          showConfirmButton: false
        });
      }
    } catch (error) {
      console.error('Delete error:', error);
      await Swal.fire({
        icon: 'error',
        title: 'Hata!',
        text: 'Mesaj silinemedi'
      });
    }
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(async () => {
  await checkAuth();
  await fetchMessages();
  await fetchGmailMessages();
});
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