// Simplified Google Analytics Plugin

const GA_MEASUREMENT_ID = import.meta.env.VITE_GA_MEASUREMENT_ID;
const isDev = import.meta.env.DEV;

class Analytics {
  constructor() {
    this.initialized = false;
    this.init();
  }

  init() {
    if (!GA_MEASUREMENT_ID) {
      console.warn('Google Analytics ID not found');
      return;
    }

    if (isDev) {
      console.log('🔬 Development Mode: Analytics simulated');
      this.initialized = true;
      this.createMockGtag();
      return;
    }

    this.loadGoogleAnalytics();
  }

  createMockGtag() {
    window.gtag = (...args) => {
      console.log('📊 GA Event (Dev):', args);
    };
  }

  loadGoogleAnalytics() {
    try {
      // Load gtag script
      const script = document.createElement('script');
      script.async = true;
      script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`;
      script.onerror = () => {
        console.warn('📵 Google Analytics blocked by ad blocker');
        this.createMockGtag();
      };
      document.head.appendChild(script);

      // Initialize gtag
      window.dataLayer = window.dataLayer || [];
      window.gtag = function gtag() {
        window.dataLayer.push(arguments);
      };

      window.gtag('js', new Date());
      window.gtag('config', GA_MEASUREMENT_ID, {
        enhanced_measurement: true,
        send_page_view: true
      });

      this.initialized = true;
      console.log('✅ Google Analytics initialized:', GA_MEASUREMENT_ID);
    } catch (error) {
      console.warn('❌ Analytics initialization failed:', error);
      this.createMockGtag();
    }
  }

  // Core tracking methods
  trackPageView(path, title) {
    if (!window.gtag) return;

    if (isDev) {
      console.log('📄 Page View:', { path, title });
      return;
    }

    window.gtag('config', GA_MEASUREMENT_ID, {
      page_path: path,
      page_title: title || document.title,
    });
  }

  trackEvent(action, category = 'engagement', label = null, value = null) {
    if (!window.gtag) return;

    if (isDev) {
      console.log('🎯 Event:', { action, category, label, value });
      return;
    }

    const eventData = { event_category: category };
    if (label) eventData.event_label = label;
    if (value) eventData.value = value;

    window.gtag('event', action, eventData);
  }

  // Specific tracking methods
  trackContact(method = 'contact') {
    this.trackEvent('contact', 'engagement', method);
  }

  trackFormSubmit(formType) {
    this.trackEvent('form_submit', 'engagement', formType);
  }

  trackEmailClick() {
    this.trackEvent('email_click', 'engagement');
  }

  trackPhoneClick() {
    this.trackEvent('phone_click', 'engagement');
  }

  trackDownload(fileName) {
    this.trackEvent('file_download', 'downloads', fileName);
  }

  trackSocialClick(platform) {
    this.trackEvent('social_click', 'engagement', platform);
  }

  trackProjectView(projectName) {
    this.trackEvent('view_item', 'projects', projectName);
  }

  trackArticleView(articleTitle) {
    this.trackEvent('view_item', 'articles', articleTitle);
  }
}

// Create global analytics instance
const analytics = new Analytics();

// Vue composable
export function useAnalytics() {
  return {
    trackPageView: (path, title) => analytics.trackPageView(path, title),
    trackEvent: (action, category, label, value) => analytics.trackEvent(action, category, label, value),
    trackContact: (method) => analytics.trackContact(method),
    trackFormSubmit: (formType) => analytics.trackFormSubmit(formType),
    trackEmailClick: () => analytics.trackEmailClick(),
    trackPhoneClick: () => analytics.trackPhoneClick(),
    trackDownload: (fileName) => analytics.trackDownload(fileName),
    trackSocialClick: (platform) => analytics.trackSocialClick(platform),
    trackProjectView: (projectName) => analytics.trackProjectView(projectName),
    trackArticleView: (articleTitle) => analytics.trackArticleView(articleTitle),
  };
}

// Auto-track page views with Vue Router
export function setupRouterTracking(router) {
  router.afterEach((to, from) => {
    // Don't track admin routes
    if (to.path.startsWith('/admin')) return;
    
    setTimeout(() => {
      analytics.trackPageView(to.path, to.meta?.title);
    }, 100);
  });
}

// Auto-track external links and downloads
export function setupLinkTracking() {
  document.addEventListener('click', (event) => {
    const link = event.target.closest('a');
    if (!link) return;

    const href = link.getAttribute('href');
    if (!href) return;

    // Track external links
    if (href.startsWith('http') && !href.includes(window.location.hostname)) {
      analytics.trackEvent('outbound_click', 'engagement', href);
    }

    // Track downloads
    if (href.includes('.pdf') || href.includes('.doc') || href.includes('.zip')) {
      const fileName = href.split('/').pop();
      analytics.trackDownload(fileName);
    }
  });
}

export default analytics;