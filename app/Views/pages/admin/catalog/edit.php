<div style="margin-bottom:1.5rem;">
    <div style="font-size:0.875rem; margin-bottom:0.25rem;">
        <a href="/admin/catalog" style="color:var(--muted-foreground); text-decoration:none;">&larr; Kembali ke Katalog Bibliografi</a>
    </div>
    <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Edit Katalog: <?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Perbarui metadata bibliografi dan berkas lampiran buku.</p>
</div>

<form method="POST" action="/admin/catalog/<?= (int)$item['id'] ?>/edit" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem;">
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="card" style="padding:1.5rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Identitas Pokok Koleksi</h2>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Judul Lengkap Buku <span style="color:#b91c1c;">*</span></label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Penulis / Pengarang</label>
                    <input type="text" name="authors_text" value="<?= htmlspecialchars($item['authors_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    <span style="font-size:0.75rem; color:var(--muted-foreground);">Pisahkan dengan titik koma (;) jika terdapat lebih dari satu penulis.</span>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Penerbit</label>
                        <input type="text" name="publisher_name" value="<?= htmlspecialchars($item['publisher_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Tahun Terbit</label>
                        <input type="number" name="publication_year" value="<?= htmlspecialchars((string)($item['publication_year'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="form-control" min="1500" max="2100">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Edisi / Cetakan</label>
                        <input type="text" name="edition_statement" value="<?= htmlspecialchars($item['edition_statement'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Bahasa</label>
                        <select name="language_code" class="form-control">
                            <option value="id" <?= ($item['language_code'] ?? '') === 'id' ? 'selected' : '' ?>>Bahasa Indonesia (id)</option>
                            <option value="en" <?= ($item['language_code'] ?? '') === 'en' ? 'selected' : '' ?>>Bahasa Inggris (en)</option>
                            <option value="ar" <?= ($item['language_code'] ?? '') === 'ar' ? 'selected' : '' ?>>Bahasa Arab (ar)</option>
                            <option value="other" <?= ($item['language_code'] ?? '') === 'other' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Jumlah Halaman Fisik</label>
                    <input type="number" name="physical_pages" value="<?= htmlspecialchars((string)($item['physical_pages'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="form-control" min="1">
                </div>

                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Sinopsis / Deskripsi Buku</label>
                    <textarea name="synopsis" rows="5" class="form-control"><?= htmlspecialchars($item['synopsis'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="card" style="padding:1.5rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Klasifikasi & Identifikasi</h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Kode DDC</label>
                        <input type="text" name="ddc_code" value="<?= htmlspecialchars($item['ddc_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Nomor Panggil Dasar</label>
                        <input type="text" name="call_number_base" value="<?= htmlspecialchars($item['call_number_base'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">ISBN-13</label>
                        <input type="text" name="isbn_13" value="<?= htmlspecialchars($item['isbn_13'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">ISBN-10</label>
                        <input type="text" name="isbn_10" value="<?= htmlspecialchars($item['isbn_10'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">ISSN</label>
                        <input type="text" name="issn" value="<?= htmlspecialchars($item['issn'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Format Koleksi</label>
                        <select name="media_type" class="form-control">
                            <option value="PRINTED_BOOK" <?= ($item['media_type'] ?? '') === 'PRINTED_BOOK' ? 'selected' : '' ?>>Buku Cetak Fisik</option>
                            <option value="DIGITAL_EBOOK" <?= ($item['media_type'] ?? '') === 'DIGITAL_EBOOK' ? 'selected' : '' ?>>E-Book Digital Murni</option>
                            <option value="HYBRID" <?= ($item['media_type'] ?? '') === 'HYBRID' ? 'selected' : '' ?>>Hybrid (Fisik & Digital)</option>
                            <option value="THESIS" <?= ($item['media_type'] ?? '') === 'THESIS' ? 'selected' : '' ?>>Skripsi / Tugas Akhir</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Topik / Subjek Tags</label>
                    <input type="text" name="subject_tags" value="<?= htmlspecialchars($item['subject_tags'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                </div>
            </div>

            <div class="card" style="padding:1.5rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Aset Berkas & Digital</h2>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Gambar Sampul Buku</label>
                    <?php if (!empty($item['cover_image_path'])): ?>
                        <div style="display:flex; align-items:center; gap:1rem; margin-bottom:0.5rem;">
                            <img src="<?= htmlspecialchars($item['cover_image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:50px; height:70px; object-fit:cover; border-radius:4px; border:1px solid var(--border);">
                            <span style="font-size:0.75rem; color:var(--muted-foreground);">Sampul saat ini aktif. Unggah berkas baru di bawah jika ingin menggantinya.</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="form-control">
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Dokumen Digital (PDF Protected Reader)</label>
                    <?php if (!empty($digitalAsset)): ?>
                        <div style="background-color:#f8fafc; border:1px solid var(--border); border-radius:var(--radius); padding:0.5rem 0.75rem; margin-bottom:0.5rem; font-size:0.75rem; color:var(--muted-foreground);">
                            Berkas digital aktif terdaftar (Format: <?= htmlspecialchars($digitalAsset['file_format'] ?? 'PDF', ENT_QUOTES, 'UTF-8') ?>, Ukuran: <?= htmlspecialchars((string)round(($digitalAsset['file_size_bytes'] ?? 0) / 1024 / 1024, 2)) ?> MB). Unggah berkas baru untuk memperbarui.
                        </div>
                    <?php endif; ?>
                    <input type="file" name="digital_pdf" accept="application/pdf" class="form-control">
                </div>

                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Tingkat Hak Akses Digital</label>
                    <?php $curAccess = $digitalAsset['access_level'] ?? 'MEMBER_STREAM_ONLY'; ?>
                    <select name="access_level" class="form-control">
                        <option value="MEMBER_STREAM_ONLY" <?= $curAccess === 'MEMBER_STREAM_ONLY' ? 'selected' : '' ?>>Khusus Anggota Terverifikasi (Streaming Saja)</option>
                        <option value="PUBLIC_OPEN" <?= $curAccess === 'PUBLIC_OPEN' ? 'selected' : '' ?>>Terbuka untuk Publik</option>
                        <option value="THESIS_ABSTRACT_ONLY" <?= $curAccess === 'THESIS_ABSTRACT_ONLY' ? 'selected' : '' ?>>Terbatas (Abstrak / Bab Tertentu)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
        <a href="/admin/catalog" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
