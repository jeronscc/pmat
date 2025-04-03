document.addEventListener('DOMContentLoaded', function () {
    fetchAndRenderSaroData(
        '/api/click/fetch-saro-click',
        '.panel.project-click',
        '.balance-box p',
        '/api/click/fetch-procurement-click',
        '/api/click/fetch-ntca-by-saro'
    );
});