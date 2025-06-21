<template>
  <div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">SEO Ayarları</h1>
        <p class="mt-2 text-gray-600">Site için SEO meta etiketleri ve sosyal medya ayarlarını yönetin</p>
      </div>

      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <form v-else @submit.prevent="updateSettings" class="space-y-8">
        <!-- Meta Tags -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            Meta Etiketleri
          </h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
              <textarea v-model="settings.meta_description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Site açıklaması (155 karakter)"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
              <input v-model="settings.meta_keywords" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="anahtar, kelimeler, virgülle, ayrılmış">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Meta Author</label>
              <input v-model="settings.meta_author" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Yazar adı">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Meta Robots</label>
              <select v-model="settings.meta_robots" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="index, follow">Index, Follow</option>
                <option value="noindex, nofollow">No Index, No Follow</option>
                <option value="index, nofollow">Index, No Follow</option>
                <option value="noindex, follow">No Index, Follow</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Open Graph -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            Open Graph (Facebook)
          </h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">OG Title</label>
              <input v-model="settings.og_title" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Facebook paylaşım başlığı">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">OG Type</label>
              <select v-model="settings.og_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="website">Website</option>
                <option value="article">Article</option>
                <option value="profile">Profile</option>
              </select>
            </div>
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">OG Description</label>
              <textarea v-model="settings.og_description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Facebook paylaşım açıklaması"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">OG Image</label>
              <input v-model="settings.og_image" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="/images/og-image.jpg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">OG Locale</label>
              <input v-model="settings.og_locale" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="tr_TR">
            </div>
          </div>
        </div>

        <!-- Twitter Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
            </svg>
            Twitter Card
          </h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Card Type</label>
              <select v-model="settings.twitter_card" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="summary">Summary</option>
                <option value="summary_large_image">Summary Large Image</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Site</label>
              <input v-model="settings.twitter_site" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="@kullaniciadi">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Creator</label>
              <input v-model="settings.twitter_creator" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="@kullaniciadi">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Title</label>
              <input v-model="settings.twitter_title" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Twitter paylaşım başlığı">
            </div>
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Description</label>
              <textarea v-model="settings.twitter_description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Twitter paylaşım açıklaması"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Image</label>
              <input v-model="settings.twitter_image" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="/images/twitter-card.jpg">
            </div>
          </div>
        </div>

        <!-- Schema.org -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Schema.org Structured Data
          </h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Schema Type</label>
              <select v-model="settings.schema_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="Person">Person</option>
                <option value="Organization">Organization</option>
                <option value="Website">Website</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Schema Name</label>
              <input v-model="settings.schema_name" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Adınız veya şirket adı">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
              <input v-model="settings.schema_job_title" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="İş unvanı">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Schema URL</label>
              <input v-model="settings.schema_url" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="https://akduhan.com">
            </div>
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Same As (Social Links)</label>
              <textarea v-model="settings.schema_same_as" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="https://github.com/username,https://linkedin.com/in/username"></textarea>
            </div>
          </div>
        </div>

        <!-- Technical SEO -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Teknik SEO
          </h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Canonical URL</label>
              <input v-model="settings.canonical_url" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="https://akduhan.com">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Sitemap URL</label>
              <input v-model="settings.sitemap_url" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="/sitemap.xml">
            </div>
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Robots.txt Content</label>
              <textarea v-model="settings.robots_txt" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="User-agent: *&#10;Allow: /&#10;Sitemap: https://akduhan.com/sitemap.xml"></textarea>
            </div>
          </div>
        </div>

        <!-- Verification & Additional -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Site Doğrulama & Ek Ayarlar
          </h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Google Site Verification</label>
              <input v-model="settings.google_site_verification" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Google Search Console verification code">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bing Site Verification</label>
              <input v-model="settings.bing_site_verification" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Bing Webmaster verification code">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Yandex Site Verification</label>
              <input v-model="settings.yandex_site_verification" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Yandex Webmaster verification code">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Theme Color</label>
              <input v-model="settings.theme_color" type="color" class="w-full h-12 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Favicon URL</label>
              <input v-model="settings.favicon_url" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="/favicon.ico">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Apple Touch Icon</label>
              <input v-model="settings.apple_touch_icon" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="/images/apple-touch-icon.png">
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
          <button type="submit" :disabled="saving" class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            <span v-if="saving">Kaydediliyor...</span>
            <span v-else>SEO Ayarlarını Kaydet</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'SEO',
  setup() {
    const loading = ref(true)
    const saving = ref(false)
    const settings = ref({
      // Meta Tags
      meta_description: '',
      meta_keywords: '',
      meta_author: '',
      meta_robots: 'index, follow',
      
      // Open Graph
      og_title: '',
      og_description: '',
      og_image: '',
      og_type: 'website',
      og_locale: 'tr_TR',
      
      // Twitter Card
      twitter_card: 'summary_large_image',
      twitter_site: '',
      twitter_creator: '',
      twitter_title: '',
      twitter_description: '',
      twitter_image: '',
      
      // Schema.org
      schema_type: 'Person',
      schema_name: '',
      schema_job_title: '',
      schema_url: '',
      schema_same_as: '',
      
      // Technical SEO
      canonical_url: '',
      sitemap_url: '/sitemap.xml',
      robots_txt: '',
      
      // Verification
      google_site_verification: '',
      bing_site_verification: '',
      yandex_site_verification: '',
      
      // Additional
      favicon_url: '/favicon.ico',
      apple_touch_icon: '/images/apple-touch-icon.png',
      theme_color: '#3B82F6',
      msapplication_tilecolor: '#3B82F6'
    })

    const fetchSettings = async () => {
      try {
        const response = await axios.get('/api/seo')
        
        if (response.data.settings) {
          Object.keys(response.data.settings).forEach(key => {
            if (settings.value.hasOwnProperty(key)) {
              settings.value[key] = response.data.settings[key] || ''
            }
          })
        }
      } catch (error) {
        console.error('SEO ayarları yüklenirken hata:', error)
      } finally {
        loading.value = false
      }
    }

    const updateSettings = async () => {
      saving.value = true
      try {
        await axios.put('/api/seo', {
          settings: settings.value
        })
        alert('SEO ayarları başarıyla güncellendi!')
      } catch (error) {
        console.error('SEO ayarları güncellenirken hata:', error)
        alert('SEO ayarları güncellenirken bir hata oluştu.')
      } finally {
        saving.value = false
      }
    }

    onMounted(() => {
      fetchSettings()
    })

    return {
      loading,
      saving,
      settings,
      updateSettings
    }
  }
}
</script>