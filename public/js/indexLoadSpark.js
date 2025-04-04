document.addEventListener('DOMContentLoaded', function () {
    fetchAndRenderSaroData(
        '/api/spark/fetch-saro-spark',
        '.panel.spark',
        '.balance-box p',
        '/api/spark/fetch-procurement-spark',
        ' /api/spark/fetch-ntca-by-saro',
        '/api/spark/fetch-procurement-details'
    );
});