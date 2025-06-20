<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-4">
            <button
              @click="router.push('/admin/dashboard')"
              class="text-gray-600 hover:text-gray-900 font-medium"
            >
              ← Dashboard
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Site Ayarları</h1>
          </div>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="isLoading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Ayarlar yükleniyor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <nav class="modern-card p-4">
            <ul class="space-y-1">
              <li v-for="tab in tabs" :key="tab.id">
                <button
                  @click="activeTab = tab.id"
                  :class="`w-full flex items-center space-x-3 px-3 py-2 rounded-lg text-left transition-colors ${
                    activeTab === tab.id
                      ? 'bg-blue-100 text-blue-700'
                      : 'text-gray-600 hover:bg-gray-100'
                  }`"
                >
                  <component :is="tab.icon" class="w-5 h-5" />
                  <span class="font-medium">{{ tab.label }}</span>
                </button>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Content -->
        <div class="lg:col-span-3">
          <form @submit.prevent="handleSubmit" class="space-y-8">
            <!-- Personal Info Tab -->
            <div v-if="activeTab === 'personal'" class="modern-card p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Kişisel Bilgiler</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ad Soyad
                  </label>
                  <input
                    v-model="formData.name"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ünvan
                  </label>
                  <input
                    v-model="formData.title"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <Mail class="w-4 h-4 inline mr-1" />
                    Email
                  </label>
                  <input
                    v-model="formData.email"
                    type="email"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <Phone class="w-4 h-4 inline mr-1" />
                    Telefon
                  </label>
                  <input
                    v-model="formData.phone"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    <MapPin class="w-4 h-4 inline mr-1" />
                    Konum
                  </label>
                  <input
                    v-model="formData.location"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Biyografi
                  </label>
                  <textarea
                    v-model="formData.bio"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"
                    required
                  />
                </div>

                <!-- Homepage Texts -->
                <div class="md:col-span-2">
                  <h3 class="text-md font-medium text-gray-900 mb-4">Ana Sayfa Yazıları</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Karşılama Yazısı
                      </label>
                      <input
                        v-model="formData.heroGreeting"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="👋 Merhaba, Ben"
                        required
                      />
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alt Başlık
                      </label>
                      <input
                        v-model="formData.heroSubtitle"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="& Backend Developer"
                      />
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        İletişim Butonu
                      </label>
                      <input
                        v-model="formData.contactButtonText"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="İletişime Geç"
                        required
                      />
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Projeler Butonu
                      </label>
                      <input
                        v-model="formData.projectsButtonText"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="Projelerimi Gör"
                        required
                      />
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        CV Butonu
                      </label>
                      <input
                        v-model="formData.cvButtonText"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="CV İndir"
                        required
                      />
                    </div>

                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hero Tagline
                      </label>
                      <input
                        v-model="formData.heroTagline"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="API & Backend Sistemler Uzmanı"
                      />
                    </div>

                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hero Açıklaması
                      </label>
                      <textarea
                        v-model="formData.heroDescription"
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"
                        placeholder="Laravel, Node.js, RESTful API'lar ve modern backend teknolojileri ile güvenli ve ölçeklenebilir sistemler geliştiriyorum. API entegrasyonları ve veritabanı optimizasyonu konularında uzmanım."
                        required
                      />
                    </div>

                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hero Anahtar Kelimeler
                      </label>
                      <input
                        v-model="formData.heroKeywords"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="Laravel • Node.js • RESTful API • SQL • Backend Development"
                        required
                      />
                      <p class="text-xs text-gray-500 mt-1">Anahtar kelimeleri "•" işareti ile ayırın</p>
                    </div>
                  </div>
                </div>

                <!-- Social Media Links -->
                <div class="md:col-span-2">
                  <h3 class="text-md font-medium text-gray-900 mb-4">Sosyal Medya</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        <Github class="w-4 h-4 inline mr-1" />
                        GitHub
                      </label>
                      <input
                        v-model="formData.github"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="https://github.com/username"
                      />
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        <Linkedin class="w-4 h-4 inline mr-1" />
                        LinkedIn
                      </label>
                      <input
                        v-model="formData.linkedin"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                        placeholder="https://linkedin.com/in/username"
                      />
                    </div>

                  </div>
                </div>
              </div>
            </div>

            <!-- Site Settings Tab -->
            <div v-if="activeTab === 'site'" class="modern-card p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Site Ayarları</h2>
              
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Site Başlığı
                  </label>
                  <input
                    v-model="formData.siteTitle"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Site Açıklaması
                  </label>
                  <textarea
                    v-model="formData.siteDescription"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Anahtar Kelimeler
                  </label>
                  <input
                    v-model="formData.keywords"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    placeholder="backend developer, api development, laravel, node.js, restful api, database optimization, sql, php, javascript"
                  />
                  <p class="mt-1 text-sm text-gray-500">SEO için anahtar kelimeleri virgülle ayırarak yazın</p>
                </div>
              </div>
            </div>

            <!-- Contact Tab -->
            <div v-if="activeTab === 'contact'" class="modern-card p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">İletişim Bilgileri</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    İletişim Email
                  </label>
                  <input
                    v-model="formData.contactEmail"
                    type="email"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    İletişim Telefon
                  </label>
                  <input
                    v-model="formData.contactPhone"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  />
                </div>
              </div>
            </div>

            <!-- Files Tab -->
            <div v-if="activeTab === 'files'" class="modern-card p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">Dosya Yönetimi</h2>
              
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    CV/Resume URL
                  </label>
                  <input
                    v-model="formData.resumeUrl"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    placeholder="https://drive.google.com/file/..."
                  />
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                  <Upload class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                  <h3 class="text-lg font-medium text-gray-900 mb-2">CV Dosyası Yükle</h3>
                  <p class="text-gray-500 mb-4">PDF formatında CV dosyanızı yükleyin</p>
                  
                  <input
                    type="file"
                    accept=".pdf"
                    @change="handleCvUpload"
                    :disabled="isCvUploading"
                    class="hidden"
                    id="cv-file-upload"
                  />
                  
                  <label
                    for="cv-file-upload"
                    :class="`inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer mb-2 ${
                      isCvUploading ? 'opacity-50 cursor-not-allowed' : ''
                    }`"
                  >
                    <div v-if="isCvUploading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                    <Upload v-else class="w-4 h-4 mr-2" />
                    {{ isCvUploading ? 'Yükleniyor...' : 'Dosya Seç' }}
                  </label>
                  
                  <p class="text-xs text-gray-500">Maksimum 5MB, PDF formatı</p>
                </div>

              </div>
            </div>

            <!-- CV Management Tab -->
            <div v-if="activeTab === 'cv'" class="modern-card p-6">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">CV Yönetimi</h2>
                <div class="flex space-x-2">
                  <button
                    @click="router.push('/admin/cv/experience')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"
                  >
                    İş Deneyimi
                  </button>
                  <button
                    @click="router.push('/admin/cv/education')"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm"
                  >
                    Eğitim
                  </button>
                  <button
                    @click="router.push('/admin/skills')"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm"
                  >
                    Yetenekler
                  </button>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                  <h3 class="font-semibold text-blue-900 mb-2">İş Deneyimi</h3>
                  <p class="text-blue-700 text-sm mb-3">
                    Şirketler, pozisyonlar ve teknolojileri yönetin.
                  </p>
                  <button
                    @click="router.push('/admin/cv/experience')"
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                  >
                    Yönet →
                  </button>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                  <h3 class="font-semibold text-purple-900 mb-2">Eğitim</h3>
                  <p class="text-purple-700 text-sm mb-3">
                    Mezun olduğunuz okulları ve dereceleri yönetin.
                  </p>
                  <button
                    @click="router.push('/admin/cv/education')"
                    class="text-purple-600 hover:text-purple-800 font-medium text-sm"
                  >
                    Yönet →
                  </button>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                  <h3 class="font-semibold text-green-900 mb-2">Yetenekler</h3>
                  <p class="text-green-700 text-sm mb-3">
                    Teknik yeteneklerinizi ve seviyelerinizi yönetin.
                  </p>
                  <button
                    @click="router.push('/admin/skills')"
                    class="text-green-600 hover:text-green-800 font-medium text-sm"
                  >
                    Yönet →
                  </button>
                </div>
              </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="isSaving"
                class="bg-blue-600 hover:bg-blue-700 text-white flex items-center space-x-2 px-6 py-3 rounded-lg disabled:opacity-50"
              >
                <Save class="w-4 h-4" />
                <span>{{ isSaving ? 'Kaydediliyor...' : 'Ayarları Kaydet' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Save, Upload, User, Globe, FileText, Mail, Phone, MapPin, Github, Linkedin, Briefcase } from 'lucide-vue-next';
import { useAuth } from '../../composables/useAuth.js';
import { usePersonalInfo } from '../../composables/usePersonalInfo.js';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const { checkAuth } = useAuth();
const { fetchPersonalInfo, updatePersonalInfo, personalInfo, isLoading: personalInfoLoading } = usePersonalInfo();

const isLoading = ref(true);
const isSaving = ref(false);
const isUploading = ref(false);
const isCvUploading = ref(false);
const activeTab = ref('personal');

const tabs = [
  { id: 'personal', label: 'Kişisel Bilgiler', icon: User },
  { id: 'site', label: 'Site Ayarları', icon: Globe },
  { id: 'contact', label: 'İletişim', icon: Mail },
  { id: 'files', label: 'Dosyalar', icon: FileText },
  { id: 'cv', label: 'CV Yönetimi', icon: Briefcase }
];

const formData = ref({
  name: 'Tunahan Akduhan',
  title: 'Backend & API Developer',
  email: 'akduhant@gmail.com',
  phone: '',
  location: 'Bursa, Türkiye',
  bio: 'Bilgisayar Programcılığı mezunu, API ve backend sistemler konusunda uzmanlaşmış bir yazılım geliştiriciyim. Laravel, Node.js ve modern web teknolojileri ile güvenli, ölçeklenebilir ve performanslı API\'lar geliştiriyorum.',
  heroGreeting: '👋 Merhaba, Ben',
  heroSubtitle: '',
  heroTagline: '',
  heroDescription: 'Laravel, Node.js, RESTful API\'lar ve modern backend teknolojileri ile güvenli ve ölçeklenebilir sistemler geliştiriyorum. API entegrasyonları ve veritabanı optimizasyonu konularında uzmanım.',
  heroKeywords: 'Laravel • Node.js • RESTful API • SQL • Backend Development',
  contactButtonText: 'İletişime Geç',
  projectsButtonText: 'Projelerimi Gör',
  cvButtonText: 'CV İndir',
  github: 'https://github.com/tunahanakduhan',
  linkedin: 'https://linkedin.com/in/tunahanakduhan',
  siteTitle: 'Tunahan Akduhan - Backend & API Developer',
  siteDescription: 'Backend sistemler ve API geliştirme konusunda uzmanlaşmış yazılım geliştirici. Laravel, Node.js ve modern web teknolojileri ile güvenli ve ölçeklenebilir çözümler.',
  keywords: 'backend developer, api development, laravel, node.js, restful api, database optimization, sql, php, javascript',
  contactEmail: 'akduhant@gmail.com',
  contactPhone: '',
  resumeUrl: ''
});

const loadSettings = async () => {
  try {
    // Personal info verilerini yükle
    await fetchPersonalInfo();
    
    // Form verilerini personal info'dan doldur
    if (personalInfo.value) {
      formData.value = {
        ...formData.value,
        name: personalInfo.value.full_name || formData.value.name,
        title: personalInfo.value.title || formData.value.title,
        email: personalInfo.value.email || formData.value.email,
        phone: personalInfo.value.phone || formData.value.phone,
        location: personalInfo.value.location || formData.value.location,
        bio: personalInfo.value.bio || formData.value.bio,
        github: personalInfo.value.github_url || formData.value.github,
        linkedin: personalInfo.value.linkedin_url || formData.value.linkedin,
        siteTitle: personalInfo.value.site_title || formData.value.siteTitle,
        siteDescription: personalInfo.value.site_description || formData.value.siteDescription,
        keywords: personalInfo.value.site_keywords || formData.value.keywords,
        resumeUrl: personalInfo.value.cv_url || formData.value.resumeUrl
      };
    }
  } catch (error) {
    console.error('Error loading settings:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleSubmit = async () => {
  isSaving.value = true;
  try {
    // Form verilerini PersonalInfo API formatına dönüştür
    const personalInfoData = [
      { key: 'full_name', value: formData.value.name, type: 'text', category: 'about' },
      { key: 'title', value: formData.value.title, type: 'text', category: 'about' },
      { key: 'email', value: formData.value.email, type: 'email', category: 'contact' },
      { key: 'phone', value: formData.value.phone, type: 'phone', category: 'contact' },
      { key: 'location', value: formData.value.location, type: 'text', category: 'about' },
      { key: 'bio', value: formData.value.bio, type: 'text', category: 'about' },
      { key: 'github_url', value: formData.value.github, type: 'url', category: 'social' },
      { key: 'linkedin_url', value: formData.value.linkedin, type: 'url', category: 'social' },
      { key: 'site_title', value: formData.value.siteTitle, type: 'text', category: 'site' },
      { key: 'site_description', value: formData.value.siteDescription, type: 'text', category: 'site' },
      { key: 'site_keywords', value: formData.value.keywords, type: 'text', category: 'site' },
      { key: 'cv_url', value: formData.value.resumeUrl, type: 'url', category: 'files' }
    ];

    // Boş değerleri filtrele
    const filteredData = personalInfoData.filter(item => item.value && item.value.trim() !== '');

    // PersonalInfo API'sini kullan
    await updatePersonalInfo(filteredData);
    
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Ayarlar başarıyla kaydedildi!',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  } catch (error) {
    console.error('Error saving settings:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Ayarlar kaydedilirken bir hata oluştu!',
      confirmButtonColor: '#ef4444'
    });
  } finally {
    isSaving.value = false;
  }
};

const handleCvUpload = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  // Dosya boyutu kontrolü (5MB)
  if (file.size > 5 * 1024 * 1024) {
    Swal.fire({
      icon: 'error',
      title: 'Dosya Çok Büyük!',
      text: 'CV dosyası maksimum 5MB olabilir.',
    });
    return;
  }

  // Dosya tipi kontrolü
  if (file.type !== 'application/pdf') {
    Swal.fire({
      icon: 'error',
      title: 'Geçersiz Dosya Tipi!',
      text: 'Sadece PDF dosyaları kabul edilir.',
    });
    return;
  }

  isCvUploading.value = true;
  try {
    const formDataUpload = new FormData();
    formDataUpload.append('file', file);
    formDataUpload.append('type', 'cv');

    const response = await axios.post('/api/upload', formDataUpload, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    if (response.data.url) {
      formData.value.resumeUrl = response.data.url;
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'CV başarıyla yüklendi!',
        showConfirmButton: false,
        timer: 3000,
      });
    }
  } catch (error) {
    console.error('CV upload error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'CV yüklenirken hata oluştu!',
    });
  } finally {
    isCvUploading.value = false;
    // Input'u temizle
    event.target.value = '';
  }
};


onMounted(async () => {
  await checkAuth();
  await loadSettings();
});
</script>

<style scoped>
.modern-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200;
}
</style>