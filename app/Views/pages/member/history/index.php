<?php
declare(strict_types=1);

$metrics = $metrics ?? [
    'active_books' => 2,
    'completed_books' => 14,
    'reading_hours' => 38,
    'active_sub' => 'Target mingguan terpenuhi',
    'completed_sub' => '+3 buku lebih banyak dari 2023',
    'hours_sub' => 'Rata-rata 42 menit per hari',
];
$readingBooks = $readingBooks ?? [];
$archiveBooks = $archiveBooks ?? [];
$greetingName = $greetingName ?? 'Sarah';
$genreFilter = $genreFilter ?? 'all';
$yearFilter = $yearFilter ?? 2024;
$streak = $streak ?? [
    'days' => 6,
    'label' => '6 Hari Beruntun',
    'sub' => 'Rekor terbaik bulan ini',
];
$totalArchiveCount = $totalArchiveCount ?? 14;
?>

<div style="padding-top: 1.5rem; padding-bottom: 4rem;">
    <div class="hero-tag-badge" style="margin-bottom: 0.75rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
        <span>Jurnal & Arsip Bacaan</span>
    </div>

    <div class="history-header-row">
        <div>
            <h1 style="font-size: 2.375rem; font-weight: 800; letter-spacing: -0.03em; color: var(--foreground); line-height: 1.2; margin-bottom: 0.5rem;">
                Riwayat Membaca
            </h1>
            <p style="font-size: 1rem; color: var(--muted-foreground); max-width: 680px; margin: 0; line-height: 1.5;">
                Lacak perjalanan membacamu dan lanjutkan bacaan terakhir dengan mudah.
            </p>
        </div>

        <div class="history-header-actions">
            <button type="button" class="btn btn-secondary btn-pill" style="font-weight: 600;" onclick="window.print()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Unduh Rekap</span>
            </button>
            <button type="button" class="btn btn-secondary btn-icon btn-circle" aria-label="Pengaturan Filter">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="21" x2="4" y2="14"></line>
                    <line x1="4" y1="10" x2="4" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="3"></line>
                    <line x1="20" y1="21" x2="20" y2="16"></line>
                    <line x1="20" y1="12" x2="20" y2="3"></line>
                    <line x1="1" y1="14" x2="7" y2="14"></line>
                    <line x1="9" y1="8" x2="15" y2="8"></line>
                    <line x1="17" y1="16" x2="23" y2="16"></line>
                </svg>
            </button>
        </div>
    </div>

    <div class="history-metrics-grid">
        <div class="history-metric-card">
            <div class="history-metric-card-top">
                <span class="history-metric-label">SEDANG DIBACA</span>
                <div class="history-metric-icon-wrap" style="background-color: #EEF2FF; color: #3E49D7;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="history-metric-val" style="color: #3E49D7;">
                <?= (int)$metrics['active_books'] ?> <span class="history-metric-unit">Buku</span>
            </div>
            <div class="history-metric-sub">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span><?= htmlspecialchars((string)$metrics['active_sub'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <div class="history-metric-card">
            <div class="history-metric-card-top">
                <span class="history-metric-label">SELESAI TAHUN INI</span>
                <div class="history-metric-icon-wrap" style="background-color: #ECFDF5; color: #10B981;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
            </div>
            <div class="history-metric-val" style="color: #10B981;">
                <?= (int)$metrics['completed_books'] ?> <span class="history-metric-unit">Buku</span>
            </div>
            <div class="history-metric-sub">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path>
                </svg>
                <span><?= htmlspecialchars((string)$metrics['completed_sub'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <div class="history-metric-card">
            <div class="history-metric-card-top">
                <span class="history-metric-label">TOTAL WAKTU MEMBACA</span>
                <div class="history-metric-icon-wrap" style="background-color: #F1F5F9; color: #475569;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <div class="history-metric-val" style="color: #1E293B;">
                <?= (int)$metrics['reading_hours'] ?> <span class="history-metric-unit">Jam</span>
            </div>
            <div class="history-metric-sub">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span><?= htmlspecialchars((string)$metrics['hours_sub'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <span class="status-dot status-dot-primary"></span>
            <h2 style="font-size: 1.375rem; font-weight: 800; color: var(--foreground); margin: 0; letter-spacing: -0.02em;">
                Sedang Dibaca
            </h2>
            <span class="badge" style="background-color: #EEF2FF; color: #3E49D7; font-weight: 700; font-size: 0.75rem;">
                <?= count($readingBooks) ?> Judul
            </span>
        </div>
        <span style="font-size: 0.8125rem; color: var(--muted-foreground);">
            Sinkronisasi otomatis ke cloud reader
        </span>
    </div>

    <div class="history-reading-grid">
        <?php foreach ($readingBooks as $b): ?>
            <?php
            $rTitle = (string)$b['title'];
            $rAuthor = (string)$b['author'];
            $rCategory = (string)$b['category'];
            $rStatusDot = (string)$b['status_dot'];
            $rStatusColor = (string)$b['status_color'];
            $rLastRead = (string)$b['last_read'];
            $rPercent = (int)$b['progress_percent'];
            $rProgressText = (string)$b['progress_text'];
            $rRemaining = (string)$b['remaining_est'];
            $rActionUrl = (string)$b['action_url'];
            $rSlug = (string)$b['slug'];
            ?>
            <div class="history-reading-card">
                <div class="history-reading-cover">
                    <span style="position: absolute; top: 6px; right: 6px; font-size: 0.55rem; font-weight: 700; background: rgba(15, 23, 42, 0.75); color: #FFFFFF; padding: 2px 5px; border-radius: 4px; backdrop-filter: blur(2px);">
                        E-Book
                    </span>
                    <?php if ($rSlug === 'filosofi-teras'): ?>
                        <div style="width: 100%; height: 100%; background: linear-gradient(180deg, #F8FAFC 0%, #E2E8F0 100%); display: flex; flex-direction: column; justify-content: space-between; padding: 0.6rem 0.5rem; text-align: center; border: 1px solid rgba(0,0,0,0.06);">
                            <span style="font-size: 0.45rem; color: #64748B; font-weight: 600;">HENRY MANAMPIRING</span>
                            <span style="font-size: 0.7rem; font-weight: 800; color: #0F172A; line-height: 1.1;">FILOSOFI TERAS</span>
                            <span style="font-size: 0.45rem; color: #3E49D7; font-weight: 700;">STOISISME</span>
                        </div>
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background: linear-gradient(180deg, #0F172A 0%, #1E3A8A 100%); display: flex; flex-direction: column; justify-content: space-between; padding: 0.6rem 0.5rem; text-align: center; color: #FFFFFF;">
                            <span style="font-size: 0.45rem; opacity: 0.75; font-weight: 500;">LEILA S. CHUDORI</span>
                            <span style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.04em; line-height: 1.1;">LAUT BERCERITA</span>
                            <span style="font-size: 0.45rem; opacity: 0.65;">NOVEL</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="history-reading-body">
                    <div class="history-reading-meta-top">
                        <span style="color: var(--muted-foreground); font-weight: 600; font-size: 0.75rem;">
                            <?= htmlspecialchars($rCategory, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600; font-size: 0.75rem; color: <?= htmlspecialchars($rStatusColor, ENT_QUOTES, 'UTF-8') ?>;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: <?= htmlspecialchars($rStatusColor, ENT_QUOTES, 'UTF-8') ?>; display: inline-block;"></span>
                            <?= htmlspecialchars($rStatusDot, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <a href="/catalog/<?= urlencode($rSlug) ?>" style="text-decoration: none; color: var(--foreground);">
                        <h3 style="font-size: 1.1875rem; font-weight: 800; line-height: 1.3; margin: 0 0 0.2rem 0; letter-spacing: -0.01em;">
                            <?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?>
                        </h3>
                    </a>
                    <div style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 0.6rem;">
                        <?= htmlspecialchars($rAuthor, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 0.85rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Terakhir dibaca: <?= htmlspecialchars($rLastRead, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <div style="margin-top: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; margin-bottom: 0.35rem;">
                            <span style="color: var(--muted-foreground); font-weight: 600;">Kemajuan Membaca</span>
                            <span style="font-weight: 700; color: var(--foreground);"><?= htmlspecialchars($rProgressText, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div style="height: 6px; background-color: var(--border-subtle); border-radius: 999px; overflow: hidden; margin-bottom: 0.85rem;">
                            <div style="width: <?= (int)$rPercent ?>%; height: 100%; background-color: #3E49D7; border-radius: 999px;"></div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            <span style="font-size: 0.75rem; color: var(--muted-foreground);">
                                <?= htmlspecialchars($rRemaining, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <a href="<?= htmlspecialchars($rActionUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-pill btn-sm" style="font-weight: 600; padding: 0.45rem 1rem;">
                                <span>Lanjutkan Membaca</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="history-archive-card">
        <div class="history-archive-toolbar">
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--foreground); margin: 0; letter-spacing: -0.01em;">
                        Buku Selesai Dibaca
                    </h2>
                    <span class="badge" style="background-color: var(--border-subtle); color: var(--muted-foreground); font-size: 0.6875rem; font-weight: 700;">
                        Arsip
                    </span>
                </div>
                <div style="font-size: 0.8125rem; color: var(--muted-foreground);">
                    Koleksi literatur yang berhasil kamu selesaikan
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <select class="form-input" style="width: auto; padding: 0.45rem 1rem; border-radius: 999px; font-size: 0.8125rem; font-weight: 600;" onchange="location.href='/history?year=' + this.value + '&genre=<?= htmlspecialchars($genreFilter, ENT_QUOTES, 'UTF-8') ?>'">
                    <option value="2024" <?= $yearFilter === 2024 ? 'selected' : '' ?>>Tahun 2024 (14 Buku)</option>
                    <option value="2023" <?= $yearFilter === 2023 ? 'selected' : '' ?>>Tahun 2023 (11 Buku)</option>
                </select>

                <div class="filter-pills" style="margin: 0;">
                    <a href="/history?genre=all&year=<?= (int)$yearFilter ?>" class="filter-pill <?= $genreFilter === 'all' ? 'active' : '' ?>">Semua</a>
                    <a href="/history?genre=non-fiksi&year=<?= (int)$yearFilter ?>" class="filter-pill <?= $genreFilter === 'non-fiksi' ? 'active' : '' ?>">Non-Fiksi</a>
                    <a href="/history?genre=fiksi&year=<?= (int)$yearFilter ?>" class="filter-pill <?= $genreFilter === 'fiksi' ? 'active' : '' ?>">Fiksi</a>
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="history-archive-table">
                <thead>
                    <tr>
                        <th style="width: 44%;">BUKU & PENULIS</th>
                        <th style="width: 22%;">TANGGAL SELESAI</th>
                        <th style="width: 20%;">PENILAIAN SAYA</th>
                        <th style="width: 14%; text-align: right;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($archiveBooks)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 3rem 1rem; color: var(--muted-foreground);">
                                Tidak ada catatan buku selesai pada filter ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($archiveBooks as $ab): ?>
                            <?php
                            $aTitle = (string)$ab['title'];
                            $aAuthor = (string)$ab['author'];
                            $aDetails = (string)$ab['details'];
                            $aFinished = (string)$ab['finished_date'];
                            $aDuration = (string)$ab['duration'];
                            $aRating = (int)$ab['rating'];
                            $aNote = (string)$ab['note'];
                            $aSlug = (string)$ab['slug'];
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div style="width: 42px; height: 60px; border-radius: 4px; overflow: hidden; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.1); background-color: #F1F5F9; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 0.45rem; font-weight: 700; color: #475569; padding: 2px;">
                                            <?php if ($aSlug === 'atomic-habits'): ?>
                                                <div style="width: 100%; height: 100%; background: #FAF5FF; color: #4C1D95; display: flex; flex-direction: column; justify-content: center;">
                                                    <span>ATOMIC</span>
                                                    <span>HABITS</span>
                                                </div>
                                            <?php elseif ($aSlug === 'the-psychology-of-money'): ?>
                                                <div style="width: 100%; height: 100%; background: #0F172A; color: #FFFFFF; display: flex; flex-direction: column; justify-content: center;">
                                                    <span>MONEY</span>
                                                </div>
                                            <?php elseif ($aSlug === 'bumi-manusia'): ?>
                                                <div style="width: 100%; height: 100%; background: #FEF3C7; color: #78350F; display: flex; flex-direction: column; justify-content: center;">
                                                    <span>BUMI</span>
                                                    <span>MANUSIA</span>
                                                </div>
                                            <?php else: ?>
                                                <div style="width: 100%; height: 100%; background: #1C1917; color: #F5F5F4; display: flex; flex-direction: column; justify-content: center;">
                                                    <span>CANTIK</span>
                                                    <span>LUKA</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <a href="/catalog/<?= urlencode($aSlug) ?>" style="text-decoration: none; color: var(--foreground);">
                                                <div style="font-weight: 700; font-size: 0.9375rem; line-height: 1.3; color: var(--foreground);">
                                                    <?= htmlspecialchars($aTitle, ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            </a>
                                            <div style="font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 0.25rem;">
                                                <?= htmlspecialchars($aAuthor, ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <span class="badge" style="background-color: var(--border-subtle); color: var(--muted-foreground); font-size: 0.6875rem; padding: 2px 7px;">
                                                <?= htmlspecialchars($aDetails, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight: 700; font-size: 0.875rem; color: var(--foreground); margin-bottom: 0.15rem;">
                                        <?= htmlspecialchars($aFinished, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--muted-foreground);">
                                        <?= htmlspecialchars($aDuration, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>

                                <td>
                                    <div style="display: flex; align-items: center; gap: 2px; color: #F59E0B; margin-bottom: 0.2rem;">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="<?= $s <= $aRating ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--muted-foreground); font-style: italic;">
                                        "<?= htmlspecialchars($aNote, ENT_QUOTES, 'UTF-8') ?>"
                                    </div>
                                </td>

                                <td style="text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <a href="/catalog/<?= urlencode($aSlug) ?>" class="btn btn-secondary btn-pill btn-sm" style="font-weight: 600; font-size: 0.75rem; padding: 0.35rem 0.85rem;">
                                            Pinjam Kembali
                                        </a>
                                        <button type="button" class="btn btn-secondary btn-icon btn-circle btn-sm" aria-label="Lihat Catatan Bacaan">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.8125rem; color: var(--muted-foreground);">
                Menampilkan <?= count($archiveBooks) ?> dari <?= (int)$totalArchiveCount ?> buku selesai pada <?= (int)$yearFilter ?>
            </span>

            <div class="pagination">
                <span class="page-link" style="opacity: 0.5;">&lt;</span>
                <span class="page-link active">1</span>
                <a href="#page-2" class="page-link">2</a>
                <a href="#page-3" class="page-link">3</a>
                <a href="#page-2" class="page-link">&gt;</a>
            </div>
        </div>
    </div>

    <div class="history-reflection-banner">
        <div>
            <span style="font-size: 0.6875rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--primary); display: block; margin-bottom: 0.35rem;">
                REFLEKSI BULAN INI
            </span>
            <h3 style="font-size: 1.375rem; font-weight: 800; color: var(--foreground); margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">
                Konsistensi yang Mengagumkan, <?= htmlspecialchars($greetingName, ENT_QUOTES, 'UTF-8') ?>!
            </h3>
            <p style="font-size: 0.875rem; color: var(--muted-foreground); margin: 0; line-height: 1.55; max-width: 680px;">
                Kamu telah membaca selama 6 hari berturut-turut pada jam tenang pagi. Menuntaskan 15-20 halaman sehari akan membantumu menyelesaikan target tahunan 20 buku dalam 2 bulan ke depan.
            </p>
        </div>

        <div class="history-streak-card">
            <div class="history-streak-icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3z"></path>
                </svg>
            </div>
            <div>
                <div style="font-size: 1.05rem; font-weight: 800; color: var(--foreground); line-height: 1.2;">
                    <?= htmlspecialchars((string)$streak['label'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.15rem;">
                    <?= htmlspecialchars((string)$streak['sub'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>
    </div>
</div>
