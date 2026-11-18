<div style="margin-bottom:1.5rem;">
    <div style="font-size:0.875rem; margin-bottom:0.25rem;">
        <a href="/admin/copies" style="color:var(--muted-foreground); text-decoration:none;">&larr; Kembali ke Eksemplar Fisik</a>
    </div>
    <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Registrasi Eksemplar Fisik Baru</h1>
    <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Tambahkan eksemplar fisik buku ke rak perpustakaan beserta nomor barcode inventaris.</p>
</div>

<form method="POST" action="/admin/copies/create">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem;">
        <div class="card" style="padding:1.5rem;">
            <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Kaitkan Judul Buku</h2>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Pilih Judul Koleksi Katalog <span style="color:#b91c1c;">*</span></label>
                <?php if ($selectedBiblio): ?>
                    <input type="hidden" name="biblio_id" value="<?= (int)$selectedBiblio['id'] ?>">
                    <div style="border:1px solid var(--border); border-radius:var(--radius); padding:0.75rem; background-color:#f8fafc;">
                        <div style="font-weight:600; font-size:0.9375rem;"><?= htmlspecialchars($selectedBiblio['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                        <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">
                            Penulis: <?= htmlspecialchars($selectedBiblio['authors_text'] ?? '-', ENT_QUOTES, 'UTF-8') ?> &bull; No. Panggil: <?= htmlspecialchars($selectedBiblio['call_number_base'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                <?php else: ?>
                    <select name="biblio_id" required class="form-control">
                        <option value="">-- Pilih Judul Buku --</option>
                        <?php foreach ($allBiblios as $b): ?>
                            <option value="<?= (int)$b['id'] ?>">
                                <?= htmlspecialchars($b['title'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($b['call_number_base'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Nomor Barcode Inventaris <span style="color:#b91c1c;">*</span></label>
                <input type="text" name="inventory_barcode" value="<?= htmlspecialchars($suggestedBarcode ?? '', ENT_QUOTES, 'UTF-8') ?>" required class="form-control" style="font-family:monospace; font-weight:600;">
                <span style="font-size:0.75rem; color:var(--muted-foreground);">Kode unik untuk ditempelkan pada fisik buku dan discan barcode reader.</span>
            </div>

            <div>
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">RFID Tag EPC (Hexadecimal Opsional)</label>
                <input type="text" name="rfid_epc_hex" class="form-control" placeholder="Contoh: E28011700000020B8A1C9A4D" style="font-family:monospace;">
                <span style="font-size:0.75rem; color:var(--muted-foreground);">Biarkan kosong jika perpustakaan hanya memakai stiker barcode.</span>
            </div>
        </div>

        <div class="card" style="padding:1.5rem;">
            <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Lokasi Rak & Status Fisik</h2>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Gedung</label>
                    <input type="text" name="shelf_building" value="Gedung Utama" class="form-control">
                </div>
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Lantai</label>
                    <input type="number" name="shelf_floor" value="1" min="1" max="10" class="form-control">
                </div>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Baris Rak / Shelf Aisle <span style="color:#b91c1c;">*</span></label>
                <input type="text" name="shelf_aisle" required placeholder="Contoh: Rak 03-A Baris 2" class="form-control">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Status Ketersediaan</label>
                    <select name="availability_status" class="form-control">
                        <option value="AVAILABLE" selected>Tersedia di Rak (AVAILABLE)</option>
                        <option value="MAINTENANCE">Dalam Perbaikan (MAINTENANCE)</option>
                        <option value="RESERVED">Reservasi (RESERVED)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Kondisi Fisik</label>
                    <select name="condition_status" class="form-control">
                        <option value="EXCELLENT">Sangat Baik (EXCELLENT)</option>
                        <option value="GOOD" selected>Baik (GOOD)</option>
                        <option value="FAIR">Cukup (FAIR)</option>
                        <option value="POOR">Kurang Baik (POOR)</option>
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Tanggal Pengadaan</label>
                    <input type="date" name="acquisition_date" value="<?= date('Y-m-d') ?>" class="form-control">
                </div>
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Nilai Penggantian (Rp)</label>
                    <input type="number" name="unit_replacement_cost" value="75000" step="1000" min="0" class="form-control">
                </div>
            </div>

            <div>
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Catatan Khusus</label>
                <textarea name="notes" rows="2" class="form-control" placeholder="Catatan asal hibah, stempel cap, dsb."></textarea>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
        <a href="/admin/copies" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">Daftarkan Eksemplar</button>
    </div>
</form>
