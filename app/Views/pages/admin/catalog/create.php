<div style="margin-bottom:1.5rem;">
    <div style="font-size:0.875rem; margin-bottom:0.25rem;">
        <a href="/admin/catalog" style="color:var(--muted-foreground); text-decoration:none;">&larr; Kembali ke Katalog Bibliografi</a>
    </div>
    <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Tambah Buku Baru ke Katalog</h1>
    <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Masukkan metadata bibliografi buku sesuai standar katalog perpustakaan.</p>
</div>

<form method="POST" action="/admin/catalog/create" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem;">
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="card" style="padding:1.5rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Identitas Pokok Koleksi</h2>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Judul Lengkap Buku <span style="color:#b91c1c;">*</span></label>
                    <input type="text" name="title" required class="form-control" placeholder="Contoh: Struktur Data dan Algoritma Modern">
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Penulis / Pengarang</label>
                    <input type="text" name="authors_text" class="form-control" placeholder="Contoh: Dr. Budi Raharjo; Ir. Ahmad Fauzi">
                    <span style="font-size:0.75rem; color:var(--muted-foreground);">Pisahkan dengan titik koma (;) jika terdapat lebih dari satu penulis.</span>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Penerbit</label>
                        <input type="text" name="publisher_name" class="form-control" placeholder="Contoh: Penerbit Informatika">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Tahun Terbit</label>
                        <input type="number" name="publication_year" class="form-control" placeholder="Contoh: 2024" min="1500" max="2100">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Edisi / Cetakan</label>
                        <input type="text" name="edition_statement" class="form-control" placeholder="Contoh: Cetakan ke-2">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Bahasa</label>
                        <select name="language_code" class="form-control">
                            <option value="id" selected>Bahasa Indonesia (id)</option>
                            <option value="en">Bahasa Inggris (en)</option>
                            <option value="ar">Bahasa Arab (ar)</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Jumlah Halaman Fisik</label>
                    <input type="number" name="physical_pages" class="form-control" placeholder="Contoh: 420" min="1">
                </div>

                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Sinopsis / Deskripsi Buku</label>
                    <textarea name="synopsis" rows="5" class="form-control" placeholder="Tuliskan ringkasan pokok materi atau abstrak buku..."></textarea>
                </div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="card" style="padding:1.5rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Klasifikasi & Identifikasi</h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Kode DDC</label>
                        <input type="text" name="ddc_code" class="form-control" placeholder="Contoh: 005.133">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Nomor Panggil Dasar</label>
                        <input type="text" name="call_number_base" class="form-control" placeholder="Contoh: 005.1 RAH s">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">ISBN-13</label>
                        <input type="text" name="isbn_13" class="form-control" placeholder="Contoh: 978-623-01-0812-3">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">ISBN-10</label>
                        <input type="text" name="isbn_10" class="form-control" placeholder="Contoh: 6230108123">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">ISSN (Jika Berkala)</label>
                        <input type="text" name="issn" class="form-control" placeholder="Contoh: 2085-4552">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Format Koleksi</label>
                        <select name="media_type" class="form-control">
                            <option value="PRINTED_BOOK" selected>Buku Cetak Fisik</option>
                            <option value="DIGITAL_EBOOK">E-Book Digital Murni</option>
                            <option value="HYBRID">Hybrid (Fisik & Digital)</option>
                            <option value="THESIS">Skripsi / Tugas Akhir</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Topik / Subjek Tags</label>
                    <input type="text" name="subject_tags" class="form-control" placeholder="Contoh: Pemrograman; Algoritma; Komputer">
                </div>
            </div>

            <div class="card" style="padding:1.5rem;">
                <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Aset Berkas & Digital</h2>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Gambar Sampul Buku</label>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="form-control">
                    <span style="font-size:0.75rem; color:var(--muted-foreground);">Format JPG, PNG, atau WEBP. Rekomendasi rasio 1:1.4 (potret).</span>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Dokumen Digital (PDF Protected Reader)</label>
                    <input type="file" name="digital_pdf" accept="application/pdf" class="form-control">
                    <span style="font-size:0.75rem; color:var(--muted-foreground);">Berkas PDF buku untuk disajikan di fitur E-Book Reader terproteksi.</span>
                </div>

                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Tingkat Hak Akses Digital</label>
                    <select name="access_level" class="form-control">
                        <option value="MEMBER_STREAM_ONLY" selected>Khusus Anggota Terverifikasi (Streaming Saja)</option>
                        <option value="PUBLIC_OPEN">Terbuka untuk Publik</option>
                        <option value="THESIS_ABSTRACT_ONLY">Terbatas (Abstrak / Bab Tertentu)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
        <a href="/admin/catalog" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan ke Katalog</button>
    </div>
</form>
