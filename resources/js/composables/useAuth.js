import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const user = ref(null);
const token = ref(localStorage.getItem('authToken'));
const isLoading = ref(false);

export const useAuth = () => {
  const router = useRouter();

  const isAuthenticated = computed(() => !!token.value && !!user.value);

  const login = async (email, password) => {
    isLoading.value = true;
    try {
      const response = await axios.post('/api/auth/login', {
        email,
        password
      });

      token.value = response.data.token;
      user.value = response.data.user;
      
      localStorage.setItem('authToken', response.data.token);
      localStorage.setItem('authUser', JSON.stringify(response.data.user));
      
      // Axios header'ına token ekle
      axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
      
      return response.data;
    } catch (error) {
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async () => {
    try {
      if (token.value) {
        await axios.post('/api/auth/logout', {}, {
          headers: {
            'Authorization': `Bearer ${token.value}`
          }
        });
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      // Local state'i temizle
      token.value = null;
      user.value = null;
      
      // localStorage'ı temizle
      localStorage.removeItem('authToken');
      localStorage.removeItem('authUser');
      
      // Axios header'ından token'ı kaldır
      delete axios.defaults.headers.common['Authorization'];
      
      // Login sayfasına yönlendir
      router.push('/admin/login');
    }
  };

  const checkAuth = async () => {
    const storedToken = localStorage.getItem('authToken');
    const storedUser = localStorage.getItem('authUser');
    
    if (storedToken && storedUser) {
      token.value = storedToken;
      user.value = JSON.parse(storedUser);
      axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`;
      
      // Token'ın geçerli olup olmadığını kontrol et
      try {
        const response = await axios.get('/api/auth/me');
        user.value = response.data.user;
      } catch (error) {
        // Token geçersiz, logout yap
        await logout();
      }
    }
  };

  // Uygulama başlarken auth durumunu kontrol et
  const initAuth = async () => {
    await checkAuth();
  };

  return {
    user: computed(() => user.value),
    token: computed(() => token.value),
    isAuthenticated,
    isLoading: computed(() => isLoading.value),
    login,
    logout,
    checkAuth,
    initAuth
  };
};