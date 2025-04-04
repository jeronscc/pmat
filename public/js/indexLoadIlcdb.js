document.addEventListener('DOMContentLoaded', function () {
    fetchAndRenderSaroData(
        '/api/fetch-saro-ilcdb',
        '.panel.ilcdb',
        '.balance-box p',
        '/api/fetch-procurement-ilcdb',
        '/api/fetch-ntca-by-saro',
        '/api/fetch-procurement-details'
    );
});