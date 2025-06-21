<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <h1 class="text-2xl font-bold text-gray-900">
                Analytics Dashboard
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
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <div v-else>
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Today Visitors -->
          <div class="modern-card p-6">
            <div class="flex items-center">
              <div class="bg-blue-500 p-3 rounded-lg mr-4">
                <Users class="w-6 h-6 text-white" />
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Bugün Ziyaretçi</p>
                <div class="flex items-center space-x-2">
                  <p class="text-2xl font-bold text-gray-900">{{ analytics.visitors?.today || 0 }}</p>
                  <span :class="getChangeClass(analytics.visitors?.change_percent)">
                    {{ formatChange(analytics.visitors?.change_percent) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Today Page Views -->
          <div class="modern-card p-6">
            <div class="flex items-center">
              <div class="bg-green-500 p-3 rounded-lg mr-4">
                <Eye class="w-6 h-6 text-white" />
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Bugün Görüntüleme</p>
                <div class="flex items-center space-x-2">
                  <p class="text-2xl font-bold text-gray-900">{{ analytics.page_views?.today || 0 }}</p>
                  <span :class="getChangeClass(analytics.page_views?.change_percent)">
                    {{ formatChange(analytics.page_views?.change_percent) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Visitors -->
          <div class="modern-card p-6">
            <div class="flex items-center">
              <div class="bg-purple-500 p-3 rounded-lg mr-4">
                <TrendingUp class="w-6 h-6 text-white" />
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Toplam Ziyaretçi</p>
                <p class="text-2xl font-bold text-gray-900">{{ analytics.visitors?.total || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Total Messages -->
          <div class="modern-card p-6">
            <div class="flex items-center">
              <div class="bg-orange-500 p-3 rounded-lg mr-4">
                <MessageSquare class="w-6 h-6 text-white" />
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Toplam Mesaj</p>
                <p class="text-2xl font-bold text-gray-900">{{ analytics.content?.messages || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Visitors Chart -->
        <div class="col-span-1 lg:col-span-2 modern-card p-6">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-3 sm:space-y-0">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
              <BarChart3 class="w-5 h-5 mr-2" />
              Ziyaretçi Grafiği
            </h3>
            <select 
              v-model="chartDays" 
              @change="loadVisitorsChart"
              class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="7">Son 7 Gün</option>
              <option value="14">Son 14 Gün</option>
              <option value="30">Son 30 Gün</option>
            </select>
          </div>
          
          <!-- Responsive Chart Container -->
          <div class="h-64 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 overflow-hidden">
            <div v-if="chartLoading" class="h-full flex items-center justify-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
            <div v-else-if="visitorsChart.length === 0" class="h-full flex items-center justify-center">
              <div class="text-center text-gray-500">
                <BarChart3 class="w-12 h-12 mx-auto mb-2 opacity-50" />
                <p class="text-sm">Henüz veri bulunmuyor</p>
              </div>
            </div>
            <div v-else class="h-full p-4">
              <!-- Chart Area -->
              <div class="h-full flex items-end justify-center space-x-1 sm:space-x-2">
                <div 
                  v-for="(data, index) in visitorsChart" 
                  :key="index" 
                  class="flex flex-col items-center group relative"
                  :style="`width: ${Math.max(100 / visitorsChart.length - 1, 8)}%`"
                >
                  <!-- Bar -->
                  <div 
                    class="bg-gradient-to-t from-blue-600 to-blue-400 rounded-t-lg transition-all duration-300 hover:from-blue-700 hover:to-blue-500 min-h-[8px] w-full"
                    :style="`height: ${Math.max((data.visitors / Math.max(...visitorsChart.map(d => d.visitors)) * 180), 8)}px`"
                  ></div>
                  
                  <!-- Date Label -->
                  <span class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-top-left whitespace-nowrap">
                    {{ formatChartDate(data.date) }}
                  </span>
                  
                  <!-- Tooltip -->
                  <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                    {{ data.visitors }} ziyaretçi<br>{{ formatChartDate(data.date, true) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Chart Summary -->
          <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
              <span>Toplam: {{ visitorsChart.reduce((sum, data) => sum + data.visitors, 0) }} ziyaretçi</span>
            </div>
            <div class="flex items-center">
              <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
              <span>Ortalama: {{ Math.round(visitorsChart.reduce((sum, data) => sum + data.visitors, 0) / Math.max(visitorsChart.length, 1)) }} ziyaretçi/gün</span>
            </div>
          </div>
        </div>

        <!-- Top Pages -->
        <div class="modern-card p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <FileText class="w-5 h-5 mr-2" />
            Popüler Sayfalar
          </h3>
          <div class="space-y-3">
            <div v-for="page in topPages" :key="page.page" class="flex justify-between items-center">
              <span class="text-sm text-gray-900">{{ page.page }}</span>
              <span class="text-sm font-medium text-blue-600">{{ page.views }}</span>
            </div>
          </div>
        </div>
        </div>

        <!-- Recent Activity and Stats Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Recent Activity -->
        <div class="col-span-1 lg:col-span-2 modern-card p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <Activity class="w-5 h-5 mr-2" />
            Son Ziyaretçi Aktiviteleri
          </h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konum</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sayfa</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cihaz</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zaman</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="activity in recentActivity" :key="activity.ip" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ activity.ip }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ activity.location }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ activity.page }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ activity.device }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ activity.time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="modern-card p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <PieChart class="w-5 h-5 mr-2" />
            İçerik İstatistikleri
          </h3>
          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Toplam Makale</span>
              <span class="text-sm font-medium text-gray-900">{{ analytics.content?.articles?.total || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Yayınlanan</span>
              <span class="text-sm font-medium text-green-600">{{ analytics.content?.articles?.published || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Taslak</span>
              <span class="text-sm font-medium text-yellow-600">{{ analytics.content?.articles?.draft || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Projeler</span>
              <span class="text-sm font-medium text-gray-900">{{ analytics.content?.projects || 0 }}</span>
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
  Eye,
  LogOut,
  Activity,
  BarChart3,
  PieChart
} from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import axios from 'axios';

const router = useRouter();
const { user, logout } = useAuth();
const isLoggingOut = ref(false);
const loading = ref(true);
const chartLoading = ref(false);
const chartDays = ref(7);

// Data
const analytics = ref({});
const visitorsChart = ref([]);
const topPages = ref([]);
const recentActivity = ref([]);


// Methods
const loadAnalytics = async () => {
  try {
    const response = await axios.get('/api/analytics/overview');
    analytics.value = response.data;
  } catch (error) {
    console.error('Analytics yüklenirken hata:', error);
  }
};

const loadVisitorsChart = async () => {
  chartLoading.value = true;
  try {
    const response = await axios.get(`/api/analytics/visitors-chart?days=${chartDays.value}`);
    visitorsChart.value = response.data;
  } catch (error) {
    console.error('Ziyaretçi grafiği yüklenirken hata:', error);
  } finally {
    chartLoading.value = false;
  }
};

const loadTopPages = async () => {
  try {
    const response = await axios.get('/api/analytics/top-pages');
    topPages.value = response.data.slice(0, 5); // İlk 5 sayfa
  } catch (error) {
    console.error('Popüler sayfalar yüklenirken hata:', error);
  }
};

const loadRecentActivity = async () => {
  try {
    const response = await axios.get('/api/analytics/recent-activity');
    recentActivity.value = response.data.slice(0, 10); // İlk 10 aktivite
  } catch (error) {
    console.error('Son aktiviteler yüklenirken hata:', error);
  }
};

const getChangeClass = (percent) => {
  if (percent > 0) return 'text-green-600 text-xs font-medium';
  if (percent < 0) return 'text-red-600 text-xs font-medium';
  return 'text-gray-600 text-xs font-medium';
};

const formatChange = (percent) => {
  if (percent === 0) return '±0%';
  const sign = percent > 0 ? '+' : '';
  return `${sign}${percent}%`;
};

const formatChartDate = (dateString, full = false) => {
  const date = new Date(dateString);
  if (full) {
    return date.toLocaleDateString('tr-TR', { 
      day: 'numeric', 
      month: 'long', 
      year: 'numeric' 
    });
  }
  return date.toLocaleDateString('tr-TR', { 
    day: 'numeric', 
    month: 'short' 
  });
};

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


onMounted(async () => {
  loading.value = true;
  try {
    // Auth durumunu kontrol et
    const { checkAuth } = useAuth();
    await checkAuth();
    
    // Parallel olarak tüm verileri yükle
    await Promise.all([
      loadAnalytics(),
      loadVisitorsChart(),
      loadTopPages(),
      loadRecentActivity()
    ]);
  } catch (error) {
    console.error('Dashboard verileri yüklenirken hata:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200;
}
</style>
