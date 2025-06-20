<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Dynamic SEO Meta Tags -->
        <title id="page-title">{{ config('app.name', 'Laravel') }}</title>
        <meta name="description" id="page-description" content="">
        <meta name="keywords" id="page-keywords" content="">
        <meta name="author" id="page-author" content="">
        <meta name="robots" id="page-robots" content="index, follow">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" id="og-title" content="">
        <meta property="og:description" id="og-description" content="">
        <meta property="og:image" id="og-image" content="">
        <meta property="og:url" id="og-url" content="{{ url()->current() }}">
        <meta property="og:type" id="og-type" content="website">
        <meta property="og:locale" id="og-locale" content="tr_TR">
        <meta property="og:site_name" content="{{ config('app.name', 'Laravel') }}">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" id="twitter-card" content="summary_large_image">
        <meta name="twitter:site" id="twitter-site" content="">
        <meta name="twitter:creator" id="twitter-creator" content="">
        <meta name="twitter:title" id="twitter-title" content="">
        <meta name="twitter:description" id="twitter-description" content="">
        <meta name="twitter:image" id="twitter-image" content="">
        
        <!-- Additional Meta Tags -->
        <link rel="canonical" id="canonical-url" href="{{ url()->current() }}">
        <link rel="icon" id="favicon" href="/favicon.ico" type="image/x-icon">
        <link rel="apple-touch-icon" id="apple-touch-icon" href="/images/apple-touch-icon.png">
        <meta name="theme-color" id="theme-color" content="#3B82F6">
        <meta name="msapplication-TileColor" content="#3B82F6">
        
        <!-- Verification Meta Tags -->
        <meta name="google-site-verification" id="google-verification" content="">
        <meta name="msvalidate.01" id="bing-verification" content="">
        <meta name="yandex-verification" id="yandex-verification" content="">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app"></div>
        
        <!-- Schema.org Structured Data -->
        <script type="application/ld+json" id="schema-data">
        {
            "@context": "https://schema.org",
            "@type": "Person",
            "name": "",
            "jobTitle": "",
            "url": "",
            "sameAs": []
        }
        </script>
    </body>
</html>
