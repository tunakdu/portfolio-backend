<template>
  <div class="fixed bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 z-50">
    <div class="relative">
      <div class="bg-black/10 backdrop-blur-2xl border border-white/20 rounded-2xl p-2 sm:p-3 shadow-2xl">
        <div class="flex items-center justify-center space-x-1 sm:space-x-2">
          <!-- Normal Menu Items -->
          <div
            v-for="(item, index) in menuItems"
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
              class="relative p-1.5 sm:p-2 rounded-xl transition-all duration-300 cursor-pointer group"
            >
              <div
                :class="[
                  'w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-all duration-300',
                  isActive(item.href)
                    ? `bg-gradient-to-br ${item.color} shadow-lg border border-white/30 scale-105`
                    : `bg-gradient-to-br ${item.color} hover:scale-105 backdrop-blur-sm border border-white/20 shadow-sm opacity-80 hover:opacity-100`
                ]"
              >
                <component :is="item.icon" :class="['w-4 h-4 sm:w-5 sm:h-5 transition-all duration-300', 'text-white drop-shadow-sm']" />
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

          <!-- Mobile Admin Menu Toggle - sadece mobilde göster -->
          <div v-if="showAdminRoutes" class="sm:hidden flex items-center mx-1">
            <div class="w-px h-6 bg-white/40"></div>
          </div>
          
          <div v-if="showAdminRoutes" class="sm:hidden relative">
            <button
              @click="toggleMobileAdmin"
              class="relative p-1.5 rounded-xl transition-all duration-300 cursor-pointer"
            >
              <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-slate-400 via-gray-500 to-zinc-500 shadow-lg border border-white/20">
                <component :is="mobileAdminOpen ? 'X' : 'Menu'" class="w-4 h-4 text-white drop-shadow-sm" />
              </div>
            </button>
            
            <!-- Mobile Admin Dropdown -->
            <div
              v-if="mobileAdminOpen"
              class="absolute bottom-full mb-3 left-1/2 transform -translate-x-1/2 bg-gray-900/95 backdrop-blur-md rounded-xl p-2 min-w-48"
            >
              <div class="space-y-1">
                <button
                  v-for="item in adminItems"
                  :key="item.href"
                  @click="() => { router.push(item.href); mobileAdminOpen = false; }"
                  class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                >
                  <div :class="`w-8 h-8 rounded-lg bg-gradient-to-br ${item.color} flex items-center justify-center`">
                    <component :is="item.icon" class="w-4 h-4 text-white" />
                  </div>
                  <span class="text-white text-sm font-medium">{{ item.label }}</span>
                </button>
                <div class="border-t border-white/20 my-2"></div>
                <button
                  @click="() => { handleLogout(); mobileAdminOpen = false; }"
                  class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-red-500/20 transition-colors"
                >
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                    <LogOut class="w-4 h-4 text-white" />
                  </div>
                  <span class="text-white text-sm font-medium">Çıkış Yap</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Desktop Admin Items - sadece desktop'ta göster -->
          <div v-if="showAdminRoutes" class="hidden sm:flex items-center mx-2">
            <div class="w-px h-6 bg-white/40"></div>
          </div>
          
          <!-- Admin Items -->
          <template v-if="showAdminRoutes">
            <div
              v-for="(item, index) in adminItems"
              :key="item.href"
              class="relative hidden sm:block"
              @mouseenter="hoveredIndex = menuItems.length + index"
              @mouseleave="hoveredIndex = null"
            >
              <div
                v-if="hoveredIndex === menuItems.length + index"
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
                :animate="{ scale: hoveredIndex === menuItems.length + index ? 1.1 : 1, y: hoveredIndex === menuItems.length + index ? -3 : 0 }"
                :transition="{ duration: 0.2, ease: 'easeOut' }"
                class="relative p-2 rounded-xl transition-all duration-300 cursor-pointer group"
              >
                <div
                  :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300',
                    isActive(item.href)
                      ? `bg-gradient-to-br ${item.color} shadow-lg border border-white/30 scale-105`
                      : `bg-gradient-to-br ${item.color} hover:scale-105 backdrop-blur-sm border border-white/20 shadow-sm opacity-80 hover:opacity-100`
                  ]"
                >
                  <component :is="item.icon" :class="['w-5 h-5 transition-all duration-300', 'text-white drop-shadow-sm']" />
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
            <div class="flex items-center mx-2">
              <div class="w-px h-6 bg-white/40"></div>
            </div>
          </template>

          <!-- Logout Button - Admin olduğunda göster - sadece desktop -->
          <div v-if="showAdminRoutes" class="relative hidden sm:block">
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
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 hover:scale-105 shadow-lg backdrop-blur-sm border border-white/20 transition-all duration-300 opacity-90 hover:opacity-100">
                <LogOut class="w-5 h-5 text-white drop-shadow-sm" />
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
import { Home, User, FolderOpen, Mail, Settings, MessageSquare, PlusCircle, BarChart3, LogOut, Code2, Rss, Newspaper, Menu, X } from 'lucide-vue-next';
import { useAuth } from '../composables/useAuth.js';
import Swal from 'sweetalert2';

const hoveredIndex = ref(null);
const mobileAdminOpen = ref(false);
const route = useRoute();
const router = useRouter();
const { isAuthenticated, logout } = useAuth();

const toggleMobileAdmin = () => {
  mobileAdminOpen.value = !mobileAdminOpen.value;
};

const menuItems = [
  { icon: Home, href: "/", label: "Ana Sayfa", color: "from-indigo-500 via-blue-500 to-cyan-500" },
  { icon: User, href: "/about", label: "Hakkımda", color: "from-emerald-500 via-green-500 to-teal-500" },
  { icon: FolderOpen, href: "/projects", label: "Projeler", color: "from-violet-500 via-purple-500 to-fuchsia-500" },
  { icon: Rss, href: "/blog", label: "Blog", color: "from-amber-500 via-orange-500 to-red-500" },
  { icon: Mail, href: "/contact", label: "İletişim", color: "from-rose-500 via-pink-500 to-red-500" },
];

const adminItems = [
  { icon: BarChart3, href: "/admin/dashboard", label: "Dashboard", color: "from-orange-400 via-orange-500 to-amber-500" },
  { icon: FolderOpen, href: "/admin/projects", label: "Projeler", color: "from-purple-400 via-violet-500 to-indigo-500" },
  { icon: Newspaper, href: "/admin/articles", label: "Makaleler", color: "from-teal-400 via-cyan-500 to-blue-500" },
  { icon: MessageSquare, href: "/admin/messages", label: "Mesajlar", color: "from-sky-400 via-blue-500 to-indigo-500" },
  { icon: Settings, href: "/admin/settings", label: "Ayarlar", color: "from-slate-400 via-gray-500 to-zinc-500" },
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

const isActive = (href) => route.path === href;
</script>
