import { ref, onMounted } from 'vue'
import axios from 'axios'

export const useSEO = () => {
  const seoSettings = ref({})
  const loading = ref(false)

  const fetchSEOSettings = async () => {
    loading.value = true
    try {
      const response = await axios.get('/api/personal-info/category/seo')
      
      // SEO ayarlarını key-value formatından obje formatına çevir
      if (response.data && response.data.settings) {
        response.data.settings.forEach(setting => {
          seoSettings.value[setting.key] = setting.value
        })
      }
    } catch (error) {
      console.error('SEO ayarları yüklenirken hata:', error)
    } finally {
      loading.value = false
    }
  }

  const updatePageSEO = (pageData = {}) => {
    const settings = seoSettings.value

    // Title güncelle
    const title = pageData.title || settings.og_title || settings.schema_name || 'Portfolio'
    document.title = title
    updateMetaTag('page-title', title)
    updateMetaTag('og-title', title)
    updateMetaTag('twitter-title', title)

    // Description güncelle
    const description = pageData.description || settings.meta_description || ''
    updateMetaTag('page-description', description)
    updateMetaTag('og-description', description)
    updateMetaTag('twitter-description', description)

    // Keywords güncelle
    const keywords = pageData.keywords || settings.meta_keywords || ''
    updateMetaTag('page-keywords', keywords)

    // Author güncelle
    const author = settings.meta_author || settings.schema_name || ''
    updateMetaTag('page-author', author)

    // Robots güncelle
    const robots = settings.meta_robots || 'index, follow'
    updateMetaTag('page-robots', robots)

    // Open Graph image güncelle
    const ogImage = pageData.image || settings.og_image || '/images/og-image.jpg'
    updateMetaTag('og-image', ogImage)
    updateMetaTag('twitter-image', ogImage)

    // OG Type güncelle
    const ogType = pageData.type || settings.og_type || 'website'
    updateMetaTag('og-type', ogType)

    // OG Locale güncelle
    const ogLocale = settings.og_locale || 'tr_TR'
    updateMetaTag('og-locale', ogLocale)

    // Twitter Card güncelle
    const twitterCard = settings.twitter_card || 'summary_large_image'
    updateMetaTag('twitter-card', twitterCard)

    // Twitter Site güncelle
    const twitterSite = settings.twitter_site || ''
    updateMetaTag('twitter-site', twitterSite)

    // Twitter Creator güncelle
    const twitterCreator = settings.twitter_creator || ''
    updateMetaTag('twitter-creator', twitterCreator)

    // Canonical URL güncelle
    const canonicalUrl = pageData.canonical || settings.canonical_url || window.location.href
    updateLinkTag('canonical-url', canonicalUrl)

    // Favicon güncelle
    const favicon = settings.favicon_url || '/favicon.ico'
    updateLinkTag('favicon', favicon)

    // Apple Touch Icon güncelle
    const appleTouchIcon = settings.apple_touch_icon || '/images/apple-touch-icon.png'
    updateLinkTag('apple-touch-icon', appleTouchIcon)

    // Theme Color güncelle
    const themeColor = settings.theme_color || '#3B82F6'
    updateMetaTag('theme-color', themeColor)

    // Verification tags güncelle
    const googleVerification = settings.google_site_verification || ''
    updateMetaTag('google-verification', googleVerification)

    const bingVerification = settings.bing_site_verification || ''
    updateMetaTag('bing-verification', bingVerification)

    const yandexVerification = settings.yandex_site_verification || ''
    updateMetaTag('yandex-verification', yandexVerification)

    // Schema.org structured data güncelle
    updateSchemaData(pageData)

    // OG URL güncelle (her zaman current URL)
    updateMetaTag('og-url', window.location.href)
  }

  const updateMetaTag = (id, content) => {
    const element = document.getElementById(id)
    if (element) {
      if (element.tagName === 'TITLE') {
        element.textContent = content
      } else {
        element.setAttribute('content', content)
      }
    }
  }

  const updateLinkTag = (id, href) => {
    const element = document.getElementById(id)
    if (element) {
      element.setAttribute('href', href)
    }
  }

  const updateSchemaData = (pageData = {}) => {
    const settings = seoSettings.value
    const schemaElement = document.getElementById('schema-data')
    
    if (schemaElement) {
      const schemaData = {
        "@context": "https://schema.org",
        "@type": settings.schema_type || "Person",
        "name": settings.schema_name || "",
        "jobTitle": settings.schema_job_title || "",
        "url": settings.schema_url || window.location.origin,
        "sameAs": settings.schema_same_as ? settings.schema_same_as.split(',').map(url => url.trim()) : []
      }

      // Sayfa özel schema verileri varsa ekle
      if (pageData.schema) {
        Object.assign(schemaData, pageData.schema)
      }

      schemaElement.textContent = JSON.stringify(schemaData)
    }
  }

  // Component mount olduğunda SEO ayarlarını yükle
  onMounted(() => {
    fetchSEOSettings()
  })

  return {
    seoSettings,
    loading,
    fetchSEOSettings,
    updatePageSEO
  }
}