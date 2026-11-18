<?php
declare(strict_types=1);
?>
<section style="padding:3rem 0; text-align:center; max-width:760px; margin:0 auto;">
    <div class="badge" style="margin-bottom:1rem;">Katalog & Sirkulasi Digital Terpadu</div>
    <h1 style="font-size:2.75rem; letter-spacing:-0.03em; margin-bottom:1rem; line-height:1.15;">
        Akses Koleksi Perpustakaan Fisik & Digital dalam Satu Sistem
    </h1>
    <p style="font-size:1.125rem; color:var(--muted-foreground); margin-bottom:2rem;">
        Telusuri ketersediaan buku di rak fisik secara presisi, reservasi antrean peminjaman, serta nikmati bahan bacaan digital langsung melalui peramban Anda.
    </p>

    <form action="/catalog" method="GET" style="display:flex; gap:0.5rem; max-width:560px; margin:0 auto 1.5rem auto;">
        <input type="text" name="q" class="input" placeholder="Cari judul, pengarang, ISBN, atau topik pustaka..." autofocus>
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <div style="display:flex; justify-content:center; gap:1.5rem; font-size:0.875rem; color:var(--muted-foreground);">
        <span>Koleksi Cetak</span>
        <span>&bull;</span>
        <span>E-Book Terproteksi</span>
        <span>&bull;</span>
        <span>Repositori Ilmiah</span>
        <span>&bull;</span>
        <span>Pemesanan Ruang Diskusi</span>
    </div>
</section>

<section style="margin-top:1.5rem; margin-bottom:3rem;">
    <div class="grid grid-cols-2" style="max-width:600px; margin:0 auto;">
        <?php
        $statTitle = 'Judul Bibliografi';
        $statValue = (string)($totalTitles ?? 0);
        $statDescription = 'Total judul buku dan repositori ilmiah terdaftar';
        require dirname(__DIR__, 2) . '/components/stat-card.php';

        $statTitle = 'Eksemplar di Rak';
        $statValue = (string)($totalCopies ?? 0);
        $statDescription = 'Buku fisik tersedia langsung untuk dipinjam';
        require dirname(__DIR__, 2) . '/components/stat-card.php';
        ?>
    </div>
</section>

<section style="margin-top:3rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.5rem;">
        <div>
            <h2>Koleksi Pilihan Terbaru</h2>
            <p style="color:var(--muted-foreground); font-size:0.875rem;">Buku dan dokumen ilmiah yang baru ditambahkan ke dalam sistem.</p>
        </div>
        <a href="/catalog" class="btn btn-outline btn-sm">Lihat Semua Katalog</a>
    </div>

    <div class="grid grid-cols-3">
        <?php if (!empty($sampleBooks)): ?>
            <?php foreach ($sampleBooks as $book): ?>
                <?php require dirname(__DIR__, 2) . '/components/book-card.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section style="margin-top:4rem; padding-top:3rem; border-top:1px solid var(--border);">
    <div class="grid grid-cols-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pencarian Berbasis Rak</h3>
                <p class="card-description">Mengetahui posisi fisik buku, nomor panggil, gedung, lantai, dan ketersediaan langsung di rak sebelum berkunjung.</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pembaca E-Book Terintegrasi</h3>
                <p class="card-description">Membaca dokumen digital dan jurnal dengan proteksi aliran biner terenkripsi dan sinkronisasi markah halaman.</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sirkulasi & Reservasi Cepat</h3>
                <p class="card-description">Peminjaman kilat dengan pemindaian kode batang, antrean buku otomatis, dan perpanjangan mandiri transparan.</p>
            </div>
        </div>
    </div>
</section>
