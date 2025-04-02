document.addEventListener('DOMContentLoaded', function() {
    fetchAndRenderSaroData(
        '/api/dtc/fetch-saro-dtc',
        '.panel.dtc',
        '.balance-box p',
        '/api/dtc/fetch-procurement-dtc',
        ' /api/dtc/fetch-ntca-by-saro'
    );
});