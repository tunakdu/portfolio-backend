<template>
  <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50">
    <div class="relative">
      <div class="bg-black/10 backdrop-blur-2xl border border-white/20 rounded-2xl p-3 shadow-2xl">
        <div class="flex items-center justify-center space-x-2">
          <!-- Menu Items -->
          <div
            v-for="(item, index) in currentItems"
            :key="item.href"
            class="relative"
            @mouseenter="hoveredIndex = index"
            @mouseleave="hoveredIndex = null"
          >
            <div
              v-if="hoveredIndex === index"
              v-motion
              :initial="{ opacity: 0, y: 10, scale: 0.8 }"
              :animate="{ opacity: 1, y: -10, scale: 1 }"
              :exit="{ opacity: 0, y: 10, scale: 0.8 }"
              :transition="{ duration: 0.15, ease: 'easeOut' }"
              class="absolute bottom-full mb-3 left-1/2 transform -translate-x-1/2 bg-gray-900/90 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap font-medium"
            >
              {{ item.label }}
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900/90"></div>
            </div>

            <button
              @click="() => router.push(item.href)"
              v-motion
              :hover="{ scale: 1.15, y: -6 }"
              :tap="{ scale: 0.95 }"
              :animate="{ scale: hoveredIndex === index ? 1.1 : 1, y: hoveredIndex === index ? -3 : 0 }"
              :transition="{ duration: 0.2, ease: 'easeOut' }"
              class="relative p-2 rounded-xl transition-all duration-300 cursor-pointer group"
            >
              <div
                :class="[
                  'w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300',
                  isActive(item.href)
                    ? `bg-gradient-to-br ${item.color} shadow-md border border-white/30`
                    : 'bg-white/75 hover:bg-white/85 backdrop-blur-sm border border-white/40 shadow-sm'
                ]"
              >
                <component :is="item.icon" :class="['w-5 h-5 transition-all duration-300', isActive(item.href) ? 'text-white' : 'text-gray-700']" />
              </div>

              <div
                v-if="isActive(item.href)"
                v-motion
                layoutId="active-indicator"
                class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-white rounded-full"
                :transition="{ duration: 0.2 }"
              ></div>
            </button>
          </div>

          <!-- Admin Items ve Logout ayırıcısı -->
          <div v-if="showAdminRoutes && currentItems.length > menuItems.length" class="w-px h-6 bg-white/40 mx-3"></div>

          <!-- Logout Button - Admin olduğunda göster -->
          <div v-if="showAdminRoutes" class="relative">
            <div
              v-if="hoveredIndex === -1"
              class="absolute bottom-full mb-3 left-1/2 transform -translate-x-1/2 bg-gray-900/90 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap font-medium"
            >
              Çıkış Yap
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900/90"></div>
            </div>

            <button
              @click="handleLogout"
              @mouseenter="hoveredIndex = -1"
              @mouseleave="hoveredIndex = null"
              class="relative p-2 rounded-xl transition-all duration-300 cursor-pointer group"
            >
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 shadow-md backdrop-blur-sm border border-white/30 transition-all duration-300">
                <LogOut class="w-5 h-5 text-white" />
              </div>
            </button>
          </div>
        </div>
      </div>
      <div class="absolute top-full left-0 right-0 h-8 bg-gradient-to-b from-white/5 to-transparent rounded-b-2xl transform scale-y-[-1] opacity-30 blur-sm"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Home, User, FolderOpen, Mail, Settings, MessageSquare, PlusCircle, BarChart3, LogOut, Code2 } from 'lucide-vue-next';
import { useAuth } from '../composables/useAuth.js';
import Swal from 'sweetalert2';

const hoveredIndex = ref(null);
const route = useRoute();
const router = useRouter();
const { isAuthenticated, logout } = useAuth();

const menuItems = [
  { icon: Home, href: "/", label: "Ana Sayfa", color: "from-blue-500 to-blue-600" },
  { icon: User, href: "/about", label: "Hakkımda", color: "from-green-500 to-green-600" },
  { icon: FolderOpen, href: "/projects", label: "Projeler", color: "from-purple-500 to-purple-600" },
  { icon: Mail, href: "/contact", label: "İletişim", color: "from-red-500 to-red-600" },
];

const adminItems = [
  { icon: BarChart3, href: "/admin/dashboard", label: "Dashboard", color: "from-orange-500 to-orange-600" },
  { icon: FolderOpen, href: "/admin/projects", label: "Projeler", color: "from-purple-500 to-purple-600" },
  { icon: PlusCircle, href: "/admin/projects/new", label: "Yeni Proje", color: "from-emerald-500 to-emerald-600" },
  { icon: MessageSquare, href: "/admin/messages", label: "Mesajlar", color: "from-cyan-500 to-cyan-600" },
  { icon: Settings, href: "/admin/settings", label: "Ayarlar", color: "from-gray-500 to-gray-600" },
];

const handleLogout = async () => {
  const result = await Swal.fire({
    title: 'Çıkış Yapmak İstiyor musunuz?',
    text: 'Admin panelinden çıkış yapacaksınız.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Evet, Çıkış Yap',
    cancelButtonText: 'İptal',
    background: 'rgba(255, 255, 255, 0.95)',
    backdrop: 'rgba(0, 0, 0, 0.4)'
  });

  if (result.isConfirmed) {
    await logout();
    
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Başarıyla çıkış yapıldı!',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });
  }
};

// Admin routes'ları sadece authenticated olduğunda göster
const showAdminRoutes = computed(() => isAuthenticated.value);
const currentItems = computed(() => [...menuItems, ...(showAdminRoutes.value ? adminItems : [])]);

const isActive = (href) => route.path === href;
</script>
