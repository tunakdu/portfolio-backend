<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <h1 class="text-2xl font-bold text-gray-900">
                Admin Panel
              </h1>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">Hoş geldiniz, {{ user?.name || 'Admin' }}</span>
            <button
              @click="handleLogout"
              :disabled="isLoggingOut"
              class="flex items-center space-x-2 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors disabled:opacity-50"
            >
              <LogOut class="w-4 h-4" />
              <span>{{ isLoggingOut ? "Çıkış yapılıyor..." : "Çıkış Yap" }}</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Section -->
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">
          Hoş Geldiniz! 👋
        </h2>
        <p class="text-gray-600">
          Portfolio sitenizi yönetmek için gerekli tüm araçlar burada.
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div v-for="stat in stats" :key="stat.title" class="modern-card p-6">
          <div class="flex items-center">
            <div :class="`${stat.color} p-3 rounded-lg mr-4`">
              <component :is="stat.icon" class="w-6 h-6 text-white" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">
                {{ stat.title }}
              </p>
              <div class="flex items-center space-x-2">
                <p class="text-2xl font-bold text-gray-900">
                  {{ stat.value }}
                </p>
                <span class="text-sm text-green-600 font-medium">
                  {{ stat.change }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-2">
          <div class="modern-card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <Activity class="w-5 h-5 mr-2" />
              Hızlı Eylemler
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <button
                v-for="action in quickActions"
                :key="action.title"
                :class="`${action.color} text-white p-6 rounded-xl text-left transition-all duration-200 transform hover:scale-105`"
                @click="handleQuickAction(action.href)"
              >
                <component :is="action.icon" class="w-8 h-8 mb-3" />
                <h4 class="font-semibold mb-2">{{ action.title }}</h4>
                <p class="text-sm text-white/80">{{ action.description }}</p>
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Activities -->
        <div>
          <div class="modern-card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <Calendar class="w-5 h-5 mr-2" />
              Son Aktiviteler
            </h3>
            <div class="space-y-4">
              <div v-for="(activity, index) in recentActivities" :key="index" class="flex items-start space-x-3">
                <div class="bg-gray-100 p-2 rounded-lg">
                  <component :is="activity.icon" class="w-4 h-4 text-gray-600" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900">
                    {{ activity.action }}
                  </p>
                  <p class="text-sm text-gray-500 truncate">
                    {{ activity.description }}
                  </p>
                  <p class="text-xs text-gray-400 mt-1">
                    {{ activity.time }}
                  </p>
                </div>
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
import {
  Users,
  FileText,
  Settings,
  Plus,
  MessageSquare,
  TrendingUp,
  Star,
  Eye,
  Calendar,
  LogOut,
  Activity
} from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';

const router = useRouter();
const { user, logout } = useAuth();
const isLoggingOut = ref(false);

const stats = [
  {
    title: "Toplam Projeler",
    value: "12",
    change: "+2",
    icon: FileText,
    color: "bg-blue-500",
  },
  {
    title: "Gelen Mesajlar",
    value: "8",
    change: "+3",
    icon: MessageSquare,
    color: "bg-green-500",
  },
  {
    title: "Site Görüntüleme",
    value: "1,250",
    change: "+15%",
    icon: Eye,
    color: "bg-purple-500",
  },
  {
    title: "Bu Ay Ziyaret",
    value: "340",
    change: "+8%",
    icon: TrendingUp,
    color: "bg-orange-500",
  },
];

const quickActions = [
  {
    title: "Yeni Proje Ekle",
    description: "Portfolio'ya yeni proje ekleyin",
    icon: Plus,
    href: "/admin/projects/new",
    color: "bg-blue-600 hover:bg-blue-700",
  },
  {
    title: "Mesajları Görüntüle",
    description: "Gelen mesajları inceleyin",
    icon: MessageSquare,
    href: "/admin/messages",
    color: "bg-green-600 hover:bg-green-700",
  },
  {
    title: "Yetenekleri Yönet",
    description: "Skill'lerinizi düzenleyin",
    icon: Star,
    href: "/admin/skills",
    color: "bg-indigo-600 hover:bg-indigo-700",
  },
  {
    title: "Site Ayarları",
    description: "Genel ayarları düzenleyin",
    icon: Settings,
    href: "/admin/settings",
    color: "bg-purple-600 hover:bg-purple-700",
  },
  {
    title: "Tüm Projeler",
    description: "Proje listesini görüntüleyin",
    icon: FileText,
    href: "/admin/projects",
    color: "bg-orange-600 hover:bg-orange-700",
  },
];

const recentActivities = [
  {
    action: "Yeni mesaj geldi",
    description: "John Doe'dan proje hakkında soru",
    time: "5 dakika önce",
    icon: MessageSquare,
  },
  {
    action: "Proje güncellendi",
    description: "E-commerce projesi düzenlendi",
    time: "2 saat önce",
    icon: FileText,
  },
  {
    action: "Site ziyaret edildi",
    description: "15 yeni ziyaretçi",
    time: "6 saat önce",
    icon: Users,
  },
  {
    action: "Portfolio güncellemesi",
    description: "Yeni CV yüklendi",
    time: "1 gün önce",
    icon: Activity,
  },
];

const handleLogout = async () => {
  isLoggingOut.value = true;
  
  try {
    await logout();
  } catch (error) {
    console.error('Logout error:', error);
  } finally {
    isLoggingOut.value = false;
  }
};

const handleQuickAction = (href) => {
  router.push(href);
};

onMounted(async () => {
  // Auth durumunu kontrol et
  const { checkAuth } = useAuth();
  await checkAuth();
});
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200;
}
</style>