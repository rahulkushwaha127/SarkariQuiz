{{-- PWA: manifest + theme color for "Add to Home Screen" / install --}}
<link rel="manifest" href="{{ url('manifest.json') }}">
<meta name="theme-color" content="{{ $pwaThemeColor ?? '#4f46e5' }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
