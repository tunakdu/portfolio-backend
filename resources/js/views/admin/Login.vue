<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50/30 to-purple-50/30 py-12 px-4">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <!-- Gradient orbs -->
      <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-br from-blue-300/15 to-purple-300/15 rounded-full blur-3xl login-orb-1" />
      <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-gradient-to-br from-purple-300/10 to-pink-300/10 rounded-full blur-3xl login-orb-2" />
    </div>

    <div class="relative max-w-md w-full login-card">
      <!-- Main Card -->
      <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/30 p-8">
        <!-- Login Logo -->
        <div class="relative w-20 h-20 flex items-center justify-center mx-auto mb-6 login-logo">
          <!-- Outer ring -->
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-400/30 via-purple-400/30 to-blue-600/30 backdrop-blur-sm border border-white/40 login-logo-ring" />
          
          <!-- Inner circle -->
          <div class="relative w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-xl flex items-center justify-center login-logo-center">
            <Shield class="w-6 h-6 text-white" />
          </div>
          
          <!-- Floating particles -->
          <div v-for="i in 6" :key="i" 
               class="absolute w-1 h-1 bg-blue-400 rounded-full login-particle"
               :style="{
                 left: `${50 + 35 * Math.cos((i * Math.PI * 2) / 6)}%`,
                 top: `${50 + 35 * Math.sin((i * Math.PI * 2) / 6)}%`,
                 animationDelay: (i * 0.3) + 's'
               }" />
        </div>
        
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 bg-clip-text text-transparent mb-2">
            Admin Paneli
          </h2>
          <p class="text-gray-600">
            Devam etmek için giriş yapın
          </p>
        </div>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <!-- Email Input -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email Adresi
            </label>
            <div class="relative">
              <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                required
                v-model="email"
                class="w-full px-4 py-3 bg-white/50 border border-gray-300/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 backdrop-blur-sm"
                placeholder="admin@example.com"
              />
            </div>
          </div>

          <!-- Password Input -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Şifre
            </label>
            <div class="relative">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                v-model="password"
                class="w-full px-4 py-3 bg-white/50 border border-gray-300/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 backdrop-blur-sm pr-12"
                placeholder="••••••••"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
              >
                <EyeOff v-if="showPassword" class="h-5 w-5" />
                <Eye v-else class="h-5 w-5" />
              </button>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-3 text-red-600 text-sm text-center">
            {{ error }}
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <div v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
              <Lock v-else class="w-5 h-5" />
              {{ loading ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Bottom decoration -->
      <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
          Güvenli yönetim paneli
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Eye, EyeOff, Shield, Lock } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';

const router = useRouter();
const { login } = useAuth();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const error = ref('');
const loading = ref(false);

const handleSubmit = async () => {
  error.value = '';
  loading.value = true;

  try {
    await login(email.value, password.value);
    router.push('/admin/dashboard');
  } catch (err) {
    error.value = err.response?.data?.message || 'Giriş yapılamadı';
    console.error('Login error:', err);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Login Animations */
@keyframes login-orb-1 {
  0%, 100% { transform: translate(0px, 0px) scale(1); }
  50% { transform: translate(50px, -30px) scale(1.2); }
}

@keyframes login-orb-2 {
  0%, 100% { transform: translate(0px, 0px) scale(1); }
  50% { transform: translate(-40px, 40px) scale(1.3); }
}

@keyframes login-logo-ring {
  0% { transform: rotate(0deg) scale(1); }
  50% { transform: rotate(180deg) scale(1.05); }
  100% { transform: rotate(360deg) scale(1); }
}

@keyframes login-logo-glow {
  0%, 100% { box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4); }
  50% { box-shadow: 0 12px 35px rgba(147, 51, 234, 0.6); }
}

@keyframes login-particle {
  0%, 100% { opacity: 0; transform: scale(0.5); }
  50% { opacity: 1; transform: scale(1.5); }
}

@keyframes login-card-enter {
  0% { opacity: 0; transform: translateY(30px) scale(0.95); }
  100% { opacity: 1; transform: translateY(0px) scale(1); }
}

/* Apply animations */
.login-orb-1 {
  animation: login-orb-1 20s ease-in-out infinite;
}

.login-orb-2 {
  animation: login-orb-2 18s ease-in-out infinite 3s;
}

.login-logo {
  animation: login-card-enter 0.8s ease-out;
}

.login-logo-ring {
  animation: login-logo-ring 20s linear infinite;
}

.login-logo-center {
  animation: login-logo-glow 3s ease-in-out infinite;
}

.login-particle {
  animation: login-particle 2s ease-in-out infinite;
}

.login-card {
  animation: login-card-enter 0.8s ease-out;
}
</style>