<?php
declare(strict_types=1);

$user = $user ?? null;
$greetingName = $greetingName ?? 'Sarah';
$currentlyReading = $currentlyReading ?? [];
$curatedWeeklyBooks = $curatedWeeklyBooks ?? [];
$popularBooks = $popularBooks ?? [];
$totalTitles = $totalTitles ?? 1200;
?>

<div style="padding-top: 2rem; padding-bottom: 4rem;">
    <section style="text-align: center; max-width: 820px; margin: 0 auto;">
        <div class="hero-tag-badge">
            <span class="status-dot status-dot-success"></span>
            <span>RuangBaca Siap Temani Harimu</span>
        </div>

        <h1 style="font-size: 2.625rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1.2; margin-bottom: 1rem; color: var(--foreground);">
            Selamat Pagi, <span style="color: var(--primary);"><?= htmlspecialchars((string)$greetingName, ENT_QUOTES, 'UTF-8') ?></span>! Temukan buku hangat untuk menemanimu hari ini.
        </h1>

        <p style="font-size: 1.0625rem; color: var(--muted-foreground); max-width: 620px; margin: 0 auto 2.25rem auto; line-height: 1.6;">
            Perpustakaan digital yang ramah dan tenang. Ribuan buku berkualitas siap dibaca langsung dari perambanmu tanpa ribet.
        </p>

        <form action="/catalog" method="GET" class="search-bar-hero" style="max-width: 620px; margin: 0 auto;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted-foreground); flex-shrink: 0;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="q" placeholder="Cari judul buku, penulis favorit, atau topik menarik..." autocomplete="off">
            <button type="submit" class="btn btn-primary btn-pill" style="padding: 0.55rem 1.35rem; font-weight: 600;">Cari</button>
        </form>

        <div class="trending-pills-row">
            <span style="color: var(--muted-foreground); font-weight: 500;">Tren saat ini:</span>
            <a href="/catalog?q=Fiksi+Populer" class="trending-pill-link">Fiksi Populer</a>
            <a href="/catalog?q=Self+Improvement" class="trending-pill-link">Self Improvement</a>
            <a href="/catalog?q=Teknologi" class="trending-pill-link">Teknologi</a>
            <a href="/catalog?q=Sains" class="trending-pill-link">Sains &amp; Filosofi</a>
            <a href="/catalog?q=Sastra+Klasik" class="trending-pill-link">Sastra Klasik</a>
        </div>

        <div style="margin-top: 1.25rem;">
            <a href="/catalog" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.875rem; font-weight: 600; color: var(--foreground); text-decoration: none;">
                <span>Jelajahi Semua Koleksi</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </section>

    <?php if (!empty($currentlyReading)): ?>
        <section style="max-width: 980px; margin: 2rem auto 0 auto;">
            <div class="reading-now-card">
                <div class="reading-now-left">
                    <div class="reading-now-thumb">
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #E2E8F0 0%, #CBD5E1 100%); display: flex; flex-direction: column; justify-content: space-between; padding: 0.4rem; text-align: center; border-radius: var(--radius-sm);">
                            <span style="font-size: 0.5rem; font-weight: 700; color: #475569; letter-spacing: 0.05em; text-transform: uppercase;">FILOSOFI</span>
                            <span style="font-size: 0.625rem; font-weight: 800; color: #1E293B; line-height: 1.1;">TERAS</span>
                            <span style="font-size: 0.45rem; color: #64748B;">H. Manampiring</span>
                        </div>
                    </div>

                    <div class="reading-now-details">
                        <div class="reading-meta-row">
                            <span class="reading-badge-active"><?= htmlspecialchars((string)$currentlyReading['status_label'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span><?= htmlspecialchars((string)$currentlyReading['last_read'], ENT_QUOTES, 'UTF-8') ?></span>
                            </span>
                        </div>

                        <h2 class="reading-now-title">
                            <?= htmlspecialchars((string)$currentlyReading['title'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <div class="reading-now-author">
                            <?= htmlspecialchars((string)$currentlyReading['author'], ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div style="max-width: 380px;">
                            <div class="reading-progress-info">
                                <span>Kemajuan Membaca</span>
                                <span><?= htmlspecialchars((string)$currentlyReading['progress_text'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: <?= (int)$currentlyReading['progress_percent'] ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reading-now-actions">
                    <button type="button" class="btn btn-ghost btn-pill" style="width: 40px; height: 40px; padding: 0; border: 1px solid var(--border);" aria-label="Simpan Markah">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>
                    <a href="<?= htmlspecialchars((string)$currentlyReading['reader_url'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-pill" style="padding: 0.65rem 1.35rem; font-weight: 600;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <span>Lanjutkan Membaca</span>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section style="margin-top: 4.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 0.5rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; color: var(--foreground); margin-bottom: 0.25rem;">
                    Buku Pilihan Minggu Ini
                </h2>
                <p style="font-size: 0.875rem; color: var(--muted-foreground); margin: 0;">
                    Kurasi hangat dari pustakawan kami untukmu
                </p>
            </div>
            <a href="/catalog" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem; font-weight: 600; color: var(--foreground); text-decoration: none;">
                <span>Lihat Semua Pilihan</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        <div class="showcase-grid">
            <?php foreach ($curatedWeeklyBooks as $item): ?>
                <a href="/catalog/<?= urlencode((string)$item['slug']) ?>" class="showcase-card">
                    <div class="showcase-frame">
                        <span class="showcase-category-badge"><?= htmlspecialchars((string)$item['category'], ENT_QUOTES, 'UTF-8') ?></span>

                        <?php if ($item['slug'] === 'atomic-habits'): ?>
                            <div class="showcase-frame-device">
                                <div class="showcase-frame-inner" style="background: #FAFAFA; padding: 0.75rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                    <div style="font-size: 0.5rem; font-weight: 700; color: #64748B;">JAMES CLEAR</div>
                                    <div>
                                        <div style="font-size: 0.75rem; font-weight: 900; letter-spacing: -0.02em; color: #0F172A; line-height: 1.1;">ATOMIC</div>
                                        <div style="font-size: 0.75rem; font-weight: 900; letter-spacing: -0.02em; color: #0F172A; line-height: 1.1;">HABITS</div>
                                        <div style="width: 24px; height: 2px; background: #3E49D7; margin: 0.3rem auto;"></div>
                                        <div style="font-size: 0.45rem; color: #64748B; line-height: 1.2;">Tiny Changes, Remarkable Results</div>
                                    </div>
                                    <div style="font-size: 0.45rem; font-weight: 600; color: #94A3B8;">RuangBaca E-Reader</div>
                                </div>
                            </div>
                        <?php elseif ($item['slug'] === 'bumi-manusia'): ?>
                            <div style="width: 135px; height: 195px; background: #EFE8DA; border-radius: 6px; box-shadow: 0 8px 20px -2px rgba(120, 80, 40, 0.25); border-left: 5px solid #8C6A43; padding: 0.75rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                <div style="font-size: 0.5rem; font-weight: 700; color: #5C4033; letter-spacing: 0.05em;">PRAMOEDYA ANANTA TOER</div>
                                <div>
                                    <div style="font-size: 0.8125rem; font-weight: 900; color: #3E2723; line-height: 1.15; font-family: Georgia, serif;">BUMI MANUSIA</div>
                                    <div style="font-size: 0.45rem; color: #6D4C41; margin-top: 0.25rem;">Tetralogi Buru</div>
                                </div>
                                <div style="font-size: 0.45rem; color: #8D6E63;">Koleksi Klasik Sastra</div>
                            </div>
                        <?php elseif ($item['slug'] === 'laut-bercerita'): ?>
                            <div style="width: 160px; height: 185px; background: #0F172A; border-radius: 8px; padding: 5px; box-shadow: 0 8px 24px -2px rgba(15, 23, 42, 0.25); display: flex; flex-direction: column;">
                                <div style="background: linear-gradient(180deg, #1E3A8A 0%, #0F172A 100%); border-radius: 5px; width: 100%; height: 100%; padding: 0.65rem; display: flex; flex-direction: column; justify-content: space-between; color: #FFFFFF; text-align: center;">
                                    <div style="font-size: 0.5rem; opacity: 0.8; letter-spacing: 0.05em;">LEILA S. CHUDORI</div>
                                    <div>
                                        <div style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.02em;">LAUT</div>
                                        <div style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.02em;">BERCERITA</div>
                                    </div>
                                    <div style="font-size: 0.45rem; opacity: 0.6;">Format Digital PDF</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="width: 110px; height: 195px; background: #0F172A; border-radius: 18px; padding: 5px; box-shadow: 0 8px 24px -2px rgba(15, 23, 42, 0.25); display: flex; flex-direction: column;">
                                <div style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%); border-radius: 14px; width: 100%; height: 100%; padding: 0.65rem 0.4rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                                    <div style="font-size: 0.45rem; font-weight: 700; color: #0369A1;">DANIEL GOLEMAN</div>
                                    <div>
                                        <div style="font-size: 0.6875rem; font-weight: 800; color: #0C4A6E; line-height: 1.15;">KECERDASAN</div>
                                        <div style="font-size: 0.6875rem; font-weight: 800; color: #0C4A6E; line-height: 1.15;">EMOSIONAL</div>
                                    </div>
                                    <div style="font-size: 0.45rem; color: #0284C7;">RuangBaca Mobile</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="showcase-meta">
                        <div class="showcase-status">
                            <span class="status-dot status-dot-success"></span>
                            <span><?= htmlspecialchars((string)$item['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="showcase-title">
                            <?= htmlspecialchars((string)$item['title'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div class="showcase-author">
                            <?= htmlspecialchars((string)$item['author'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="popular-section-box">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <div class="popular-badge-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                    <span>SEDANG HANGAT DIPERBINCANGKAN</span>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; color: var(--foreground); margin: 0;">
                    Rekomendasi Terpopuler
                </h2>
            </div>
            <span style="font-size: 0.8125rem; color: var(--muted-foreground);">Diperbarui setiap pagi</span>
        </div>

        <div class="popular-grid">
            <?php foreach ($popularBooks as $item): ?>
                <a href="/catalog/<?= urlencode((string)$item['slug']) ?>" class="popular-card">
                    <button type="button" class="popular-bookmark-btn" aria-label="Markah Cepat">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>

                    <div class="popular-thumb-wrap">
                        <?php if ($item['slug'] === 'sapiens'): ?>
                            <div style="width: 80px; height: 120px; background: #FFFFFF; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 0.5rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; border: 1px solid #E2E8F0;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #475569;">YUVAL NOAH HARARI</span>
                                <span style="font-size: 0.75rem; font-weight: 900; color: #0F172A; letter-spacing: 0.05em;">SAPIENS</span>
                                <span style="font-size: 0.4rem; color: #94A3B8;">Riwayat Singkat Manusia</span>
                            </div>
                        <?php elseif ($item['slug'] === 'gadis-kretek'): ?>
                            <div style="width: 80px; height: 120px; background: #FEF9C3; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 0.5rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; border: 1px solid #FEF08A;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #713F12;">RATIH KUMALA</span>
                                <span style="font-size: 0.65rem; font-weight: 900; color: #451A03; line-height: 1.15;">GADIS KRETEK</span>
                                <span style="font-size: 0.4rem; color: #854D0E;">Novel Fiksi Sejarah</span>
                            </div>
                        <?php elseif ($item['slug'] === 'the-psychology-of-money'): ?>
                            <div style="width: 80px; height: 120px; background: #1E293B; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); padding: 0.5rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #F8FAFC;">
                                <span style="font-size: 0.4rem; font-weight: 600; color: #94A3B8;">MORGAN HOUSEL</span>
                                <span style="font-size: 0.55rem; font-weight: 800; line-height: 1.15; color: #F1F5F9;">THE PSYCHOLOGY OF MONEY</span>
                                <span style="font-size: 0.4rem; color: #38BDF8;">Pelajaran Kekayaan</span>
                            </div>
                        <?php else: ?>
                            <div style="width: 80px; height: 120px; background: #3730A3; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); padding: 0.5rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #EEF2FF;">
                                <span style="font-size: 0.45rem; font-weight: 700; color: #C7D2FE;">EKA KURNIAWAN</span>
                                <span style="font-size: 0.65rem; font-weight: 900; line-height: 1.15; color: #FFFFFF;">CANTIK ITU LUKA</span>
                                <span style="font-size: 0.4rem; color: #A5B4FC;">Realisme Magis</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="popular-title">
                        <?= htmlspecialchars((string)$item['title'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="popular-author">
                        <?= htmlspecialchars((string)$item['author'], ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="popular-meta-row">
                        <div class="popular-rating">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B" stroke-width="1">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <span><?= htmlspecialchars((string)$item['rating'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <span><?= htmlspecialchars((string)$item['readers_count'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="/catalog" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.875rem; font-weight: 600; color: var(--foreground); text-decoration: none;">
                <span>Lihat <?= htmlspecialchars(number_format((float)$totalTitles, 0, ',', '.'), ENT_QUOTES, 'UTF-8') ?>+ Buku Lainnya di Katalog</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </section>

    <section class="value-prop-banner">
        <div class="value-prop-left">
            <div class="value-prop-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                </svg>
            </div>
            <div>
                <h3 class="value-prop-title">Baca Kapan Saja, Di Mana Saja</h3>
                <p class="value-prop-desc">Pengalaman membaca sinkron otomatis di laptop, tablet, dan smartphone Anda tanpa jeda.</p>
            </div>
        </div>

        <div class="value-prop-actions">
            <a href="/guide" class="btn btn-outline btn-pill">Panduan Meminjam</a>
            <a href="/catalog" class="btn btn-primary btn-pill">Mulai Membaca</a>
        </div>
    </section>
</div>
