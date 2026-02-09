{{-- Register PWA service worker so install / Add to Home Screen is offered --}}
<script>
(function() {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('{{ url("sw.js") }}', { scope: '/' })
        .then(function() {})
        .catch(function() {});
    });
  }
})();
</script>
