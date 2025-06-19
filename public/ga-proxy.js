// Alternative Google Analytics loading for production
// This file can be served from your own domain to bypass ad blockers

(function() {
  // Only load in production
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('GA Proxy: Development mode, skipping');
    return;
  }

  const GA_ID = 'G-JTHCKS5WPH';
  
  // Load gtag from your own domain or alternative CDN
  const script = document.createElement('script');
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_ID}`;
  
  script.onerror = function() {
    console.warn('Google Analytics blocked by ad blocker');
    // Fallback to internal analytics only
    window.gtag = function() {
      console.log('GA Event (Blocked):', arguments);
    };
  };
  
  document.head.appendChild(script);
  
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', GA_ID);
  
  window.gtag = gtag;
})();