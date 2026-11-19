<?php
declare(strict_types=1);

$item = $item ?? [];
$asset = $asset ?? [];
$progress = $progress ?? null;
$streamToken = $streamToken ?? '';
$watermarkText = $watermarkText ?? 'E-Library System';
$user = $user ?? [];

$lastPage = (string)($progress['last_page_or_cfi'] ?? '1');
$completionPercent = (float)($progress['completion_percent'] ?? 0.0);
$totalPages = (int)($item['physical_pages'] ?? 100);
$streamUrl = "/reader/stream/" . (int)$asset['id'] . "?token=" . urlencode($streamToken) . "#page=" . urlencode($lastPage) . "&toolbar=0&navpanes=0";
?>
<div id="reader-container"
     data-asset-id="<?= (int)$asset['id'] ?>"
     data-watermark="<?= htmlspecialchars($watermarkText, ENT_QUOTES, 'UTF-8') ?>"
     data-current-page="<?= htmlspecialchars($lastPage, ENT_QUOTES, 'UTF-8') ?>"
     data-total-pages="<?= $totalPages ?>"
     style="display:flex; flex-direction:column; height:100vh; width:100vw; background-color:var(--background); color:var(--foreground); overflow:hidden;">

    <header style="display:flex; justify-content:space-between; align-items:center; padding:0.625rem 1.25rem; border-bottom:1px solid var(--border); background-color:var(--card); z-index:20; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:1rem;">
            <a href="/catalog/<?= urlencode((string)$item['slug']) ?>" class="btn btn-outline btn-sm">
                Kembali ke Katalog
            </a>
            <div>
                <div style="font-weight:700; font-size:0.9375rem; line-height:1.2; max-width:420px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?= htmlspecialchars((string)$item['title'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                    <?= htmlspecialchars((string)$item['authors_text'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:0.75rem;">
            <button type="button" id="btn-fullscreen" class="btn btn-outline btn-sm">
                Layar Penuh
            </button>
        </div>
    </header>

    <main id="reader-viewport" style="position:relative; flex:1; width:100%; height:100%; overflow:hidden; background-color:#525659;">
        <div id="reader-watermark-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:10;"></div>
        <iframe
            id="reader-frame"
            src="<?= htmlspecialchars($streamUrl, ENT_QUOTES, 'UTF-8') ?>"
            style="width:100%; height:100%; border:none; display:block;"
            title="Penampil Dokumen Digital Terproteksi"
        ></iframe>
    </main>

    <footer style="display:flex; justify-content:space-between; align-items:center; padding:0.5rem 1.25rem; border-top:1px solid var(--border); background-color:var(--card); font-size:0.75rem; color:var(--muted-foreground); z-index:20; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:1rem;">
            <span>Posisi Terakhir: Halaman <strong><?= htmlspecialchars($lastPage, ENT_QUOTES, 'UTF-8') ?></strong></span>
            <span>&bull;</span>
            <span id="reader-sync-indicator">Tersinkronisasi otomatis</span>
        </div>

        <div style="display:flex; align-items:center; gap:0.5rem;">
            <span class="badge badge-outline" style="font-size:0.7rem;">
                Dokumen Terproteksi Anti-Unduh
            </span>
            <span class="badge" style="font-size:0.7rem;">
                <?= htmlspecialchars((string)($user['member_number'] ?? 'MEMBER'), ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
    </footer>
</div>
