<div style="margin-bottom:1.5rem;">
    <div style="font-size:0.875rem; margin-bottom:0.25rem;">
        <a href="/admin/copies" style="color:var(--muted-foreground); text-decoration:none;">&larr; Kembali ke Eksemplar Fisik</a>
    </div>
    <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Edit Eksemplar: <?= htmlspecialchars($copy['inventory_barcode'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Perbarui lokasi rak fisik, status ketersediaan, atau kondisi buku.</p>
</div>

<form method="POST" action="/admin/copies/<?= (int)$copy['id'] ?>/edit">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem;">
        <div class="card" style="padding:1.5rem;">
            <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Informasi Buku & Barcode</h2>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Judul Koleksi</label>
                <div style="border:1px solid var(--border); border-radius:var(--radius); padding:0.75rem; background-color:#f8fafc;">
                    <div style="font-weight:600; font-size:0.9375rem;"><?= htmlspecialchars($copy['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">
                        Penulis: <?= htmlspecialchars($copy['authors_text'] ?? '-', ENT_QUOTES, 'UTF-8') ?> &bull; No. Panggil: <?= htmlspecialchars($copy['call_number_base'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Nomor Barcode Inventaris <span style="color:#b91c1c;">*</span></label>
                <input type="text" name="inventory_barcode" value="<?= htmlspecialchars($copy['inventory_barcode'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required class="form-control" style="font-family:monospace; font-weight:600;">
            </div>

            <div>
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">RFID Tag EPC (Hexadecimal Opsional)</label>
                <input type="text" name="rfid_epc_hex" value="<?= htmlspecialchars($copy['rfid_epc_hex'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control" style="font-family:monospace;">
            </div>
        </div>

        <div class="card" style="padding:1.5rem;">
            <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Lokasi Rak & Status Fisik</h2>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Gedung</label>
                    <input type="text" name="shelf_building" value="<?= htmlspecialchars($copy['shelf_building'] ?? 'Gedung Utama', ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                </div>
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Lantai</label>
                    <input type="number" name="shelf_floor" value="<?= (int)($copy['shelf_floor'] ?? 1) ?>" min="1" max="10" class="form-control">
                </div>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Baris Rak / Shelf Aisle <span style="color:#b91c1c;">*</span></label>
                <input type="text" name="shelf_aisle" value="<?= htmlspecialchars($copy['shelf_aisle'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required class="form-control">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Status Ketersediaan</label>
                    <?php $st = $copy['availability_status'] ?? 'AVAILABLE'; ?>
                    <select name="availability_status" class="form-control">
                        <option value="AVAILABLE" <?= $st === 'AVAILABLE' ? 'selected' : '' ?>>Tersedia di Rak (AVAILABLE)</option>
                        <option value="BORROWED" <?= $st === 'BORROWED' ? 'selected' : '' ?>>Sedang Dipinjam (BORROWED)</option>
                        <option value="RESERVED" <?= $st === 'RESERVED' ? 'selected' : '' ?>>Reservasi (RESERVED)</option>
                        <option value="MAINTENANCE" <?= $st === 'MAINTENANCE' ? 'selected' : '' ?>>Dalam Perbaikan (MAINTENANCE)</option>
                        <option value="DAMAGED" <?= $st === 'DAMAGED' ? 'selected' : '' ?>>Rusak Berat (DAMAGED)</option>
                        <option value="LOST" <?= $st === 'LOST' ? 'selected' : '' ?>>Hilang (LOST)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Kondisi Fisik</label>
                    <?php $cd = $copy['condition_status'] ?? 'GOOD'; ?>
                    <select name="condition_status" class="form-control">
                        <option value="EXCELLENT" <?= $cd === 'EXCELLENT' ? 'selected' : '' ?>>Sangat Baik (EXCELLENT)</option>
                        <option value="GOOD" <?= $cd === 'GOOD' ? 'selected' : '' ?>>Baik (GOOD)</option>
                        <option value="FAIR" <?= $cd === 'FAIR' ? 'selected' : '' ?>>Cukup (FAIR)</option>
                        <option value="POOR" <?= $cd === 'POOR' ? 'selected' : '' ?>>Kurang Baik (POOR)</option>
                        <option value="DAMAGED" <?= $cd === 'DAMAGED' ? 'selected' : '' ?>>Rusak (DAMAGED)</option>
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Tanggal Pengadaan</label>
                    <input type="date" name="acquisition_date" value="<?= htmlspecialchars((string)($copy['acquisition_date'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                </div>
                <div>
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Nilai Penggantian (Rp)</label>
                    <input type="number" name="unit_replacement_cost" value="<?= htmlspecialchars((string)($copy['unit_replacement_cost'] ?? '75000'), ENT_QUOTES, 'UTF-8') ?>" step="1000" min="0" class="form-control">
                </div>
            </div>

            <div>
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.35rem;">Catatan Khusus</label>
                <textarea name="notes" rows="2" class="form-control"><?= htmlspecialchars($copy['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
        <a href="/admin/copies" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
