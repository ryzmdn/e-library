<?php
declare(strict_types=1);

$item = $item ?? [];
$copies = $copies ?? [];
$digitalAsset = $digitalAsset ?? null;
$canAccessDigital = $canAccessDigital ?? false;
$relatedItems = $relatedItems ?? [];
$user = $user ?? null;

$slug = (string)($item['slug'] ?? 'filosofi-teras');
$title = (string)($item['title'] ?? 'Filosofi Teras');
$subtitle = (string)($item['subtitle'] ?? '');
$authors = (string)($item['authors_text'] ?? 'Henry Manampiring');
$publisher = (string)($item['publisher_name'] ?? 'Penerbit Buku Kompas');
$year = (string)($item['publication_year'] ?? '2019');
$edition = (string)($item['edition_statement'] ?? 'Cetakan Ke-14 (2019)');
$language = (string)($item['language_code'] ?? 'id-ID');
$pages = (int)($item['physical_pages'] ?? 320);
$isbn = (string)($item['isbn_13'] ?? '978-602-412-518-9');
$ddc = (string)($item['ddc_code'] ?? '100');
$callNumber = (string)($item['call_number_base'] ?? '100 MAN f');
$synopsis = (string)($item['synopsis'] ?? '');
$tags = array_filter(array_map('trim', explode(',', (string)($item['subject_tags'] ?? ''))));
$mediaType = (string)($item['media_type'] ?? 'HYBRID');
$availableCopies = (int)($item['available_physical_copies'] ?? 3);
$totalCopies = (int)($item['total_physical_copies'] ?? 4);
$ratingAvg = (float)($item['rating_avg'] ?? 4.9);
$ratingCount = (int)($item['rating_count'] ?? 2410);
$readersCountText = (string)($item['readers_count_text'] ?? '14.8k');

$primaryCategory = !empty($tags) ? $tags[0] : 'Pengembangan Diri & Filsafat';
?>

<div style="padding-top: 1.5rem; padding-bottom: 4rem;">
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 1.5rem; flex-wrap: wrap;">
        <a href="/" style="color: inherit; text-decoration: none;">Beranda</a>
        <span>/</span>
        <a href="/catalog" style="color: inherit; text-decoration: none;">Katalog Buku</a>
        <span>/</span>
        <a href="/catalog?q=<?= urlencode($primaryCategory) ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($primaryCategory, ENT_QUOTES, 'UTF-8') ?></a>
        <span>/</span>
        <span style="color: var(--foreground); font-weight: 600;"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></span>
    </nav>

    <div class="detail-layout">
        <aside class="detail-left-col">
            <div class="detail-cover-card">
                <div class="detail-cover-mockup">
                    <span class="cover-format-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span>ePub &amp; PDF</span>
                    </span>

                    <div style="width: 220px; height: 320px; background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%); border-radius: 8px; box-shadow: 0 12px 32px -4px rgba(30, 58, 138, 0.4); display: flex; flex-direction: column; justify-content: space-between; padding: 1.5rem 1.25rem; color: #FFFFFF; text-align: center; border-left: 6px solid #172554;">
                        <div>
                            <div style="font-size: 0.625rem; font-weight: 700; letter-spacing: 0.15em; opacity: 0.85; text-transform: uppercase;">
                                <?= htmlspecialchars($primaryCategory, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div>
                            <div style="width: 54px; height: 54px; border-radius: 50%; border: 2px solid rgba(255, 255, 255, 0.3); margin: 0 auto 0.75rem auto; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.1);">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="7" r="4"></circle>
                                    <path d="M5.5 21a6.5 6.5 0 0 1 13 0"></path>
                                </svg>
                            </div>
                            <div style="font-size: 1.35rem; font-weight: 900; letter-spacing: -0.01em; line-height: 1.15; font-family: Georgia, serif;">
                                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php if ($subtitle !== ''): ?>
                                <div style="font-size: 0.55rem; opacity: 0.8; margin-top: 0.35rem; line-height: 1.2;">
                                    <?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <div style="font-size: 0.6875rem; font-weight: 600; opacity: 0.9;">
                                <?= htmlspecialchars($authors, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div style="font-size: 0.5rem; opacity: 0.6; margin-top: 0.2rem;">
                                <?= htmlspecialchars($publisher, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-action-buttons">
                    <a href="/reader/<?= urlencode($slug) ?>" class="btn btn-primary btn-pill btn-block" style="padding: 0.75rem 1.25rem; font-weight: 600; font-size: 0.9375rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <span>Baca Sekarang</span>
                    </a>

                    <?php if ($user !== null): ?>
                        <a href="/member/loans" class="btn btn-secondary btn-pill btn-block" style="padding: 0.7rem 1.25rem; font-weight: 600;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Pinjam Buku (14 Hari)</span>
                        </a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-secondary btn-pill btn-block" style="padding: 0.7rem 1.25rem; font-weight: 600;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                            <span>Masuk untuk Meminjam</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="detail-sub-actions">
                    <button type="button" class="btn btn-outline btn-pill" style="flex: 1;" aria-label="Favorit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        <span>Favorit</span>
                    </button>
                    <button type="button" class="btn btn-outline btn-pill" style="flex: 1;" aria-label="Bagikan">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                        </svg>
                        <span>Bagikan</span>
                    </button>
                </div>

                <div class="detail-feature-list">
                    <div class="detail-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                        <span>Akses instan langsung di peramban</span>
                    </div>
                    <div class="detail-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                            <line x1="12" y1="18" x2="12.01" y2="18"></line>
                        </svg>
                        <span>Sinkronisasi posisi baca antar ponsel &amp; tablet</span>
                    </div>
                    <div class="detail-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Estimasi waktu baca: 4.5 Jam</span>
                    </div>
                </div>

                <div class="detail-quote-box">
                    <div class="detail-quote-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"></path>
                        </svg>
                        <span>KUTIPAN PILIHAN</span>
                    </div>
                    <p class="detail-quote-text">
                        &ldquo;Sebagian hal berada di bawah kendali kita, dan sebagian lainnya di luar kendali kita.&rdquo;
                    </p>
                    <p class="detail-quote-author">
                        &mdash; Epictetus (Filosofi Teras, Bab 2)
                    </p>
                </div>
            </div>
        </aside>

        <main class="detail-right-col">
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                <span class="badge" style="background-color: var(--primary-subtle); color: var(--primary-subtle-text); border: 1px solid var(--primary-subtle-border);">
                    <?= htmlspecialchars($primaryCategory, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="badge" style="background-color: var(--secondary); color: var(--muted-foreground); border: 1px solid var(--border);">
                    Edisi Resmi <?= htmlspecialchars($publisher, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="badge" style="background-color: var(--status-available-bg); color: var(--status-available-text); border: 1px solid var(--status-available-border); display: inline-flex; align-items: center; gap: 0.3rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Verified Content</span>
                </span>
            </div>

            <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; color: var(--foreground); line-height: 1.2; margin-bottom: 0.5rem;">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <div style="font-size: 0.875rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">
                <span><?= htmlspecialchars($authors, ENT_QUOTES, 'UTF-8') ?></span>
                <span style="margin: 0 0.5rem;">&bull;</span>
                <span><?= htmlspecialchars($publisher, ENT_QUOTES, 'UTF-8') ?></span>
                <span style="margin: 0 0.5rem;">&bull;</span>
                <span><?= htmlspecialchars($edition, ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <div class="detail-stats-bar">
                <div class="detail-stat-item">
                    <div class="detail-stat-val-row">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B" stroke-width="1">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span><?= number_format($ratingAvg, 1) ?> <span style="font-size: 0.8125rem; font-weight: 400; color: var(--muted-foreground);">/ 5.0</span></span>
                    </div>
                    <span class="detail-stat-label"><?= number_format($ratingCount, 0, ',', '.') ?> Rating</span>
                </div>

                <div class="detail-stat-item">
                    <div class="detail-stat-val-row">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted-foreground);">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span><?= $pages > 0 ? $pages : 320 ?></span>
                    </div>
                    <span class="detail-stat-label">Halaman Cetak</span>
                </div>

                <div class="detail-stat-item">
                    <div class="detail-stat-val-row">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted-foreground);">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <span><?= $language === 'id-ID' ? 'Indonesia' : htmlspecialchars($language, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="detail-stat-label">Bahasa Utama</span>
                </div>

                <div class="detail-stat-item">
                    <div class="detail-stat-val-row">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted-foreground);">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span><?= htmlspecialchars($readersCountText, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="detail-stat-label">Kali Dibaca</span>
                </div>
            </div>

            <div class="tabs-pill-container" style="margin-bottom: 1.5rem;">
                <button type="button" class="tab-pill active" onclick="switchDetailTab('tab-synopsis', this)">Sinopsis Lengkap</button>
                <button type="button" class="tab-pill" onclick="switchDetailTab('tab-toc', this)">Daftar Isi (12 Bab)</button>
                <button type="button" class="tab-pill" onclick="switchDetailTab('tab-reviews', this)">Ulasan Pembaca (184)</button>
                <button type="button" class="tab-pill" onclick="switchDetailTab('tab-copies', this)">Salinan di Rak (<?= count($copies) ?>)</button>
            </div>

            <div id="tab-synopsis" class="tab-pane active">
                <div style="font-size: 0.9375rem; line-height: 1.7; color: var(--foreground);">
                    <p style="margin-bottom: 1rem;">
                        Lebih dari 2.000 tahun lalu, sebuah mazhab filsafat Yunani-Romawi kuno lahir untuk membantu manusia menemukan ketenteraman batin di tengah dunia yang gaduh. Mazhab itu adalah <strong>Stoisisme</strong> (atau <em>Filosofi Stoa</em>). Di Indonesia, ajaran abadi ini diterjemahkan secara membumi oleh Henry Manampiring menjadi <strong>Filosofi Teras</strong>.
                    </p>
                    <p style="margin-bottom: 1.25rem;">
                        Buku ini secara khusus ditujukan bagi masyarakat modern, khususnya generasi muda perkotaan yang rentan tercekik rasa khawatir kronis, kekecewaan sosial media, overthinking karier, dan kecemasan masa depan. Melalui gaya penulisan yang santai, jenaka, namun sarat riset psikologi kognitif modern, penulis mengupas cara mempraktikkan tiga pilar Stoik:
                    </p>
                </div>

                <div class="concept-cards-grid">
                    <div class="concept-card">
                        <div class="concept-card-header">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                            </svg>
                            <span>Dikotomi Kendali</span>
                        </div>
                        <p class="concept-card-desc">
                            Memilah dengan tegas apa yang murni dalam kendali diri (pikiran, reaksi, prinsip) vs luar kendali (opini orang, hasil, cuaca).
                        </p>
                    </div>

                    <div class="concept-card">
                        <div class="concept-card-header">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <span>Premeditatio Malorum</span>
                        </div>
                        <p class="concept-card-desc">
                            Melatih mental dengan membayangkan skenario terburuk secara tenang agar kita kebal dari kepanikan tiba-tiba.
                        </p>
                    </div>

                    <div class="concept-card">
                        <div class="concept-card-header">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <span>Amor Fati</span>
                        </div>
                        <p class="concept-card-desc">
                            Bukan sekadar pasrah menerima takdir, tetapi mencintai apapun yang terjadi dan mengubah rintangan menjadi bahan bakar kebajikan.
                        </p>
                    </div>
                </div>

                <div style="font-size: 0.9375rem; line-height: 1.7; color: var(--foreground);">
                    <p style="margin-bottom: 0;">
                        Dilengkapi dengan wawancara mendalam bersama psikiater dan psikolog klinis, buku ini membuktikan bahwa filsafat kuno bukanlah teori abstrak berdebu di perpustakaan, melainkan panduan bertahan hidup mental (<em>mental self-defense</em>) paling relevan saat ini.
                    </p>
                </div>

                <div class="publishing-info-card">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9375rem; font-weight: 700; color: var(--foreground);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Informasi Detail Penerbitan</span>
                    </div>

                    <div class="publishing-grid">
                        <div>
                            <div class="publishing-col-label">NOMOR ISBN</div>
                            <div class="publishing-col-value"><?= htmlspecialchars($isbn, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="publishing-col-sub">Digital ePub/Mobi</div>
                        </div>

                        <div>
                            <div class="publishing-col-label">TANGGAL TERBIT</div>
                            <div class="publishing-col-value">1 Januari <?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="publishing-col-sub">Edisi Indonesia</div>
                        </div>

                        <div>
                            <div class="publishing-col-label">FORMAT BERKAS</div>
                            <div class="publishing-col-value">ePub &amp; PDF Flow</div>
                            <div class="publishing-col-sub">Ukuran 12.4 MB</div>
                        </div>

                        <div>
                            <div class="publishing-col-label">KETENTUAN PEMINJAMAN</div>
                            <div class="publishing-col-value">14 Hari Peminjaman</div>
                            <div class="publishing-col-sub">Dapat diperpanjang 1x</div>
                        </div>
                    </div>

                    <div class="publishing-drm-banner">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Koleksi ini berlisensi resmi di bawah RuangBaca Digital Rights Management (DRM) untuk kenyamanan baca tanpa gangguan iklan.</span>
                    </div>
                </div>
            </div>

            <div id="tab-toc" class="tab-pane">
                <div class="card" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Daftar Bab &amp; Struktur Isi</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 1</strong> &mdash; Sebuah Survei Kekhawatiran Nasional</span>
                            <span style="color: var(--muted-foreground);">Hal 1</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 2</strong> &mdash; Sekilas tentang Filsafat Stoa</span>
                            <span style="color: var(--muted-foreground);">Hal 25</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 3</strong> &mdash; Dikotomi Kendali: Memilah Hal Penting</span>
                            <span style="color: var(--muted-foreground);">Hal 51</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 4</strong> &mdash; Menyelaraskan Pikiran dengan Nalar (Reason)</span>
                            <span style="color: var(--muted-foreground);">Hal 79</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 5</strong> &mdash; Melawan Emosi Negatif dengan Interpretasi Baru</span>
                            <span style="color: var(--muted-foreground);">Hal 105</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 6</strong> &mdash; Mengantisipasi Kemungkinan Terburuk</span>
                            <span style="color: var(--muted-foreground);">Hal 132</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 7</strong> &mdash; Berdamai dengan Masa Lalu (Amor Fati)</span>
                            <span style="color: var(--muted-foreground);">Hal 158</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 8</strong> &mdash; Hubungan Antar Manusia dan Kebaikan Sosial</span>
                            <span style="color: var(--muted-foreground);">Hal 184</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 9</strong> &mdash; Panduan untuk Para Orang Tua</span>
                            <span style="color: var(--muted-foreground);">Hal 211</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 10</strong> &mdash; Stoisisme Menghadapi Masalah Keuangan &amp; Karier</span>
                            <span style="color: var(--muted-foreground);">Hal 237</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
                            <span><strong>Bab 11</strong> &mdash; Menghadapi Kematian tanpa Rasa Gentar</span>
                            <span style="color: var(--muted-foreground);">Hal 265</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span><strong>Bab 12</strong> &mdash; Memulai Langkah Pertama di Teras</span>
                            <span style="color: var(--muted-foreground);">Hal 290</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-reviews" class="tab-pane">
                <div class="card" style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.25rem;">Ulasan Pembaca Terverifikasi</h3>
                            <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin: 0;">Rangkuman penilaian anggota yang telah menuntaskan bacaan.</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.5rem; font-weight: 800; color: var(--foreground);"><?= number_format($ratingAvg, 1) ?></span>
                            <div style="font-size: 0.75rem; color: var(--muted-foreground);">dari 5.0</div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                <strong>Andi Pratama</strong>
                                <span style="font-size: 0.75rem; color: var(--muted-foreground);">2 hari yang lalu</span>
                            </div>
                            <div style="color: #F59E0B; font-size: 0.8125rem; margin-bottom: 0.4rem;">
                                &#9733;&#9733;&#9733;&#9733;&#9733;
                            </div>
                            <p style="font-size: 0.875rem; color: var(--foreground); margin: 0; line-height: 1.5;">
                                Sangat membuka mata mengenai cara mengendalikan respon terhadap hal-hal yang di luar kendali kita. Bahasanya sangat renyah dan mudah dipraktikkan dalam keseharian kerja.
                            </p>
                        </div>

                        <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                <strong>Nabila Putri</strong>
                                <span style="font-size: 0.75rem; color: var(--muted-foreground);">1 minggu yang lalu</span>
                            </div>
                            <div style="color: #F59E0B; font-size: 0.8125rem; margin-bottom: 0.4rem;">
                                &#9733;&#9733;&#9733;&#9733;&#9733;
                            </div>
                            <p style="font-size: 0.875rem; color: var(--foreground); margin: 0; line-height: 1.5;">
                                Konsep dikotomi kendali membantu saya meredakan kecemasan overthinking sebelum ujian tugas akhir. Wajib dibaca bagi siapa saja yang sering cemas!
                            </p>
                        </div>

                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                <strong>Rian Hidayat</strong>
                                <span style="font-size: 0.75rem; color: var(--muted-foreground);">3 minggu yang lalu</span>
                            </div>
                            <div style="color: #F59E0B; font-size: 0.8125rem; margin-bottom: 0.4rem;">
                                &#9733;&#9733;&#9733;&#9733;&#9734;
                            </div>
                            <p style="font-size: 0.875rem; color: var(--foreground); margin: 0; line-height: 1.5;">
                                Penjelasan filosofisnya sangat membumi dengan analogi transportasi umum dan media sosial modern Indonesia. Sangat direkomendasikan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-copies" class="tab-pane">
                <div class="card" style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.25rem;">Inventaris Eksemplar Fisik &amp; Lokasi Rak</h3>
                            <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin: 0;">Salinan fisik yang dapat dipinjam langsung di lokasi perpustakaan.</p>
                        </div>
                        <span class="badge"><?= count($copies) ?> Salinan Terdaftar</span>
                    </div>

                    <?php if (empty($copies)): ?>
                        <div style="padding: 2rem; text-align: center; color: var(--muted-foreground); font-size: 0.875rem;">
                            Tidak ada eksemplar cetak fisik yang terdaftar untuk judul ini. Koleksi ini tersedia dalam format digital.
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="table" style="font-size: 0.8125rem;">
                                <thead>
                                    <tr>
                                        <th>No. Barcode</th>
                                        <th>Lokasi Gedung &amp; Lantai</th>
                                        <th>Nomor Rak</th>
                                        <th>Kondisi</th>
                                        <th>Status Sirkulasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($copies as $copy): ?>
                                        <tr>
                                            <td style="font-family: var(--font-mono); font-weight: 600;">
                                                <?= htmlspecialchars((string)$copy['inventory_barcode'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars((string)$copy['shelf_building'], ENT_QUOTES, 'UTF-8') ?>, Lt. <?= (int)$copy['shelf_floor'] ?>
                                            </td>
                                            <td style="font-weight: 500;">
                                                <?= htmlspecialchars((string)$copy['shelf_aisle'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-outline" style="font-size: 0.6875rem;">
                                                    <?= htmlspecialchars((string)$copy['condition_status'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php $status = (string)$copy['availability_status']; ?>
                                                <?php if ($status === 'AVAILABLE'): ?>
                                                    <span class="badge badge-available">Tersedia di Rak</span>
                                                <?php elseif ($status === 'BORROWED'): ?>
                                                    <span class="badge badge-borrowed">Sedang Dipinjam</span>
                                                <?php elseif ($status === 'RESERVED_HOLD'): ?>
                                                    <span class="badge badge-warning">Dalam Antrean Hold</span>
                                                <?php else: ?>
                                                    <span class="badge"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <section class="community-trends-card">
        <div class="community-trends-left">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3E49D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.2rem;">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--foreground); margin: 0 0 0.25rem 0;">
                    Tren Pembaca Komunitas RuangBaca
                </h3>
                <p style="font-size: 0.8125rem; color: var(--muted-foreground); margin: 0;">
                    87% pembaca menyelesaikan buku ini dalam kurun waktu kurang dari 7 hari peminjaman.
                </p>
            </div>
        </div>

        <div class="community-trends-right">
            <div style="min-width: 180px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 600; color: var(--foreground); margin-bottom: 0.35rem;">
                    <span>Tingkat Penyelesaian</span>
                    <span>87.4%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width: 87.4%;"></div>
                </div>
            </div>

            <div style="display: flex; gap: 1.5rem; font-size: 0.8125rem; color: var(--muted-foreground);">
                <div>
                    <span style="display: block; font-size: 0.75rem; color: var(--muted-foreground);">Rata-rata:</span>
                    <strong style="color: var(--foreground);">42 hal/hari</strong>
                </div>
                <div>
                    <span style="display: block; font-size: 0.75rem; color: var(--muted-foreground);">Rating kepuasan:</span>
                    <strong style="color: var(--foreground);">98%</strong>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($relatedItems)): ?>
        <section style="margin-top: 3.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; color: var(--foreground); margin-bottom: 0.25rem;">
                        Pembaca Buku Ini Juga Menikmati
                    </h2>
                    <p style="font-size: 0.875rem; color: var(--muted-foreground); margin: 0;">
                        Rekomendasi pilihan berbasis topik pengembangan nalar, ketahanan mental, dan kebiasaan baik.
                    </p>
                </div>
                <a href="/catalog" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem; font-weight: 600; color: var(--foreground); text-decoration: none;">
                    <span>Lihat Semua Rekomendasi</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                <?php foreach ($relatedItems as $book): ?>
                    <a href="/catalog/<?= urlencode((string)$book['slug']) ?>" class="card" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div style="position: relative; background: #F8FAFC; height: 260px; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border-subtle); padding: 1.25rem;">
                            <span class="badge" style="position: absolute; top: 0.85rem; right: 0.85rem; background: #FFFFFF; border: 1px solid var(--border); font-size: 0.6875rem; font-weight: 600;">
                                Tersedia
                            </span>

                            <?php if ($book['slug'] === 'atomic-habits'): ?>
                                <div style="width: 140px; height: 200px; background: #FFFFFF; border-radius: 6px; box-shadow: 0 8px 24px -4px rgba(0,0,0,0.12); padding: 0.85rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; border: 1px solid #E2E8F0;">
                                    <div style="font-size: 0.55rem; font-weight: 700; color: #64748B;">JAMES CLEAR</div>
                                    <div>
                                        <div style="font-size: 0.875rem; font-weight: 900; color: #0F172A; line-height: 1.1;">ATOMIC</div>
                                        <div style="font-size: 0.875rem; font-weight: 900; color: #0F172A; line-height: 1.1;">HABITS</div>
                                        <div style="width: 24px; height: 2px; background: #3E49D7; margin: 0.35rem auto;"></div>
                                    </div>
                                    <div style="font-size: 0.45rem; color: #94A3B8;">An Easy &amp; Proven Way</div>
                                </div>
                            <?php elseif ($book['slug'] === 'sapiens'): ?>
                                <div style="width: 140px; height: 200px; background: #FAFAF9; border-radius: 6px; box-shadow: 0 8px 24px -4px rgba(0,0,0,0.12); padding: 0.85rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; border: 1px solid #E7E5E4;">
                                    <div style="font-size: 0.55rem; font-weight: 700; color: #78716C;">YUVAL NOAH HARARI</div>
                                    <div>
                                        <div style="font-size: 0.875rem; font-weight: 900; color: #1C1917; letter-spacing: 0.05em;">SAPIENS</div>
                                        <div style="font-size: 0.45rem; color: #78716C; margin-top: 0.25rem;">A Brief History of Humankind</div>
                                    </div>
                                    <div style="font-size: 0.45rem; color: #A8A29E;">KPG Pustaka</div>
                                </div>
                            <?php else: ?>
                                <div style="width: 140px; height: 200px; background: #0F172A; border-radius: 6px; box-shadow: 0 8px 24px -4px rgba(15,23,42,0.3); padding: 0.85rem; display: flex; flex-direction: column; justify-content: space-between; text-align: center; color: #F8FAFC;">
                                    <div style="font-size: 0.5rem; font-weight: 600; color: #94A3B8;">MORGAN HOUSEL</div>
                                    <div>
                                        <div style="font-size: 0.75rem; font-weight: 900; line-height: 1.15; color: #F8FAFC;">THE PSYCHOLOGY OF MONEY</div>
                                        <div style="width: 24px; height: 2px; background: #38BDF8; margin: 0.35rem auto;"></div>
                                    </div>
                                    <div style="font-size: 0.45rem; color: #38BDF8;">Pelajaran Abadi</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 1.25rem; display: flex; flex-direction: column; flex: 1;">
                            <div style="font-size: 0.75rem; font-weight: 600; color: var(--primary); margin-bottom: 0.25rem;">
                                <?= htmlspecialchars((string)($book['category'] ?? 'Pengembangan Diri'), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--foreground); line-height: 1.3; margin: 0 0 0.25rem 0;">
                                <?= htmlspecialchars((string)$book['title'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>
                            <div style="font-size: 0.8125rem; color: var(--muted-foreground); margin-bottom: 0.75rem;">
                                <?= htmlspecialchars((string)$book['authors_text'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between; font-size: 0.8125rem;">
                                <div style="display: flex; align-items: center; gap: 0.25rem; font-weight: 600; color: var(--foreground);">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B" stroke-width="1">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    <span><?= number_format((float)($book['rating_avg'] ?? 4.8), 1) ?></span>
                                </div>
                                <button type="button" class="btn btn-ghost" style="padding: 0.25rem; color: var(--muted-foreground);" aria-label="Markah">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
function switchDetailTab(targetTabId, triggerBtn) {
  var panes = document.querySelectorAll('.tab-pane');
  panes.forEach(function (pane) {
    pane.classList.remove('active');
  });
  var buttons = document.querySelectorAll('.tabs-pill-container .tab-pill');
  buttons.forEach(function (btn) {
    btn.classList.remove('active');
  });
  var target = document.getElementById(targetTabId);
  if (target) {
    target.classList.add('active');
  }
  if (triggerBtn) {
    triggerBtn.classList.add('active');
  }
}
</script>
