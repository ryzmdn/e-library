document.addEventListener('DOMContentLoaded', () => {
    const readerContainer = document.getElementById('reader-container');
    if (!readerContainer) {
        return;
    }

    const assetId = parseInt(readerContainer.dataset.assetId || '0', 10);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const watermarkLayer = document.getElementById('reader-watermark-overlay');
    const watermarkText = readerContainer.dataset.watermark || 'E-Library System';
    const fullscreenBtn = document.getElementById('btn-fullscreen');
    const themeButtons = document.querySelectorAll('[data-reader-theme]');
    const progressText = document.getElementById('reader-sync-indicator');

    let secondsRead = 0;
    let currentPage = readerContainer.dataset.currentPage || '1';
    let totalPages = parseInt(readerContainer.dataset.totalPages || '1', 10);

    if (watermarkLayer) {
        applyWatermark(watermarkLayer, watermarkText);
    }

    window.addEventListener('contextmenu', (e) => {
        if (readerContainer.contains(e.target)) {
            e.preventDefault();
        }
    });

    window.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && ['p', 's', 'u'].includes(e.key.toLowerCase())) {
            e.preventDefault();
        }
    });

    themeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const theme = btn.dataset.readerTheme;
            setReaderTheme(theme);
        });
    });

    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        });
    }

    let isDocumentVisible = !document.hidden;
    document.addEventListener('visibilitychange', () => {
        isDocumentVisible = !document.hidden;
    });

    setInterval(() => {
        if (isDocumentVisible) {
            secondsRead += 1;
        }
    }, 1000);

    setInterval(() => {
        if (secondsRead > 0) {
            sendProgress();
        }
    }, 30000);

    window.addEventListener('beforeunload', () => {
        if (secondsRead > 0) {
            sendProgressBeacon();
        }
    });

    function sendProgress() {
        if (assetId <= 0) return;

        const percent = totalPages > 0 ? Math.min(100, Math.round((parseInt(currentPage, 10) / totalPages) * 100)) : 0;
        const payload = {
            digital_asset_id: assetId,
            page: currentPage,
            completion_percent: percent,
            seconds_read: secondsRead
        };

        fetch('/api/reader/progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && progressText) {
                progressText.textContent = 'Tersinkronisasi otomatis';
                secondsRead = 0;
            }
        })
        .catch(() => {});
    }

    function sendProgressBeacon() {
        if (assetId <= 0 || !navigator.sendBeacon) return;

        const percent = totalPages > 0 ? Math.min(100, Math.round((parseInt(currentPage, 10) / totalPages) * 100)) : 0;
        const payload = JSON.stringify({
            digital_asset_id: assetId,
            page: currentPage,
            completion_percent: percent,
            seconds_read: secondsRead,
            _csrf_token: csrfToken
        });

        navigator.sendBeacon('/api/reader/progress', new Blob([payload], { type: 'application/json' }));
    }

    function setReaderTheme(theme) {
        readerContainer.classList.remove('theme-light', 'theme-dark', 'theme-sepia');
        if (theme === 'dark') {
            readerContainer.classList.add('theme-dark');
            readerContainer.style.backgroundColor = '#121212';
            readerContainer.style.color = '#e0e0e0';
        } else if (theme === 'sepia') {
            readerContainer.classList.add('theme-sepia');
            readerContainer.style.backgroundColor = '#fbf0d9';
            readerContainer.style.color = '#382e1e';
        } else {
            readerContainer.classList.add('theme-light');
            readerContainer.style.backgroundColor = '#ffffff';
            readerContainer.style.color = '#111111';
        }
    }

    function applyWatermark(layer, text) {
        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="360" height="200">
                <text x="30" y="100" fill="rgba(120, 120, 120, 0.12)" font-size="13" font-family="sans-serif" font-weight="600" transform="rotate(-25 180 100)">
                    ${escapeHtml(text)}
                </text>
            </svg>
        `;
        const encoded = encodeURIComponent(svg.trim());
        layer.style.backgroundImage = `url("data:image/svg+xml;utf8,${encoded}")`;
        layer.style.backgroundRepeat = 'repeat';
    }

    function escapeHtml(string) {
        return String(string).replace(/[&<>"']/g, (s) => {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[s];
        });
    }
});
