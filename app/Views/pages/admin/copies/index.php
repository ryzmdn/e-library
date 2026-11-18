<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Eksemplar Fisik & Barcode</h1>
        <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Manajemen inventaris fisik per buku, lokasi rak perpustakaan, pencetakan barcode & label punggung.</p>
    </div>
    <div>
        <a href="/admin/copies/create" class="btn btn-primary" style="font-size:0.875rem;">+ Registrasi Eksemplar Baru</a>
    </div>
</div>

<div class="card" style="padding:1.25rem; margin-bottom:1.5rem;">
    <form method="GET" action="/admin/copies" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
        <div style="flex:1; min-width:240px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Pencarian Barcode / Judul / Rak</label>
            <input type="text" name="q" value="<?= htmlspecialchars($filters['keyword'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ketik nomor barcode atau judul buku..." class="form-control">
        </div>

        <div style="width:160px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Status Ketersediaan</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="AVAILABLE" <?= ($filters['status'] ?? '') === 'AVAILABLE' ? 'selected' : '' ?>>Tersedia di Rak</option>
                <option value="BORROWED" <?= ($filters['status'] ?? '') === 'BORROWED' ? 'selected' : '' ?>>Sedang Dipinjam</option>
                <option value="RESERVED" <?= ($filters['status'] ?? '') === 'RESERVED' ? 'selected' : '' ?>>Dalam Reservasi</option>
                <option value="MAINTENANCE" <?= ($filters['status'] ?? '') === 'MAINTENANCE' ? 'selected' : '' ?>>Perbaikan / Restorasi</option>
                <option value="DAMAGED" <?= ($filters['status'] ?? '') === 'DAMAGED' ? 'selected' : '' ?>>Rusak Berat</option>
                <option value="LOST" <?= ($filters['status'] ?? '') === 'LOST' ? 'selected' : '' ?>>Hilang</option>
            </select>
        </div>

        <div style="width:160px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Kondisi Fisik</label>
            <select name="condition" class="form-control">
                <option value="">Semua Kondisi</option>
                <option value="EXCELLENT" <?= ($filters['condition'] ?? '') === 'EXCELLENT' ? 'selected' : '' ?>>Sangat Baik</option>
                <option value="GOOD" <?= ($filters['condition'] ?? '') === 'GOOD' ? 'selected' : '' ?>>Baik</option>
                <option value="FAIR" <?= ($filters['condition'] ?? '') === 'FAIR' ? 'selected' : '' ?>>Cukup</option>
                <option value="POOR" <?= ($filters['condition'] ?? '') === 'POOR' ? 'selected' : '' ?>>Kurang Baik</option>
                <option value="DAMAGED" <?= ($filters['condition'] ?? '') === 'DAMAGED' ? 'selected' : '' ?>>Rusak</option>
            </select>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="/admin/copies" class="btn btn-outline">Reset</a>
        </div>
    </form>
</div>

<div class="card" style="padding:1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <div style="font-size:0.875rem; color:var(--muted-foreground);">
            Menampilkan <?= count($copies) ?> dari <?= number_format($totalCopies, 0, ',', '.') ?> eksemplar terdaftar
        </div>
    </div>

    <?php if (empty($copies)): ?>
        <div style="text-align:center; padding:3rem 1rem; color:var(--muted-foreground);">
            <div style="font-weight:600; font-size:1rem; margin-bottom:0.5rem;">Tidak ada eksemplar fisik ditemukan</div>
            <p style="font-size:0.875rem; margin:0 0 1rem 0;">Coba sesuaikan filter pencarian atau daftarkan eksemplar baru.</p>
            <a href="/admin/copies/create" class="btn btn-primary" style="font-size:0.875rem;">Registrasi Eksemplar</a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; font-size:0.875rem;">
                <thead>
                    <tr>
                        <th style="padding:0.75rem;">Barcode Inventaris</th>
                        <th style="padding:0.75rem;">Judul Buku & Penulis</th>
                        <th style="padding:0.75rem;">Lokasi Rak Fisik</th>
                        <th style="padding:0.75rem; text-align:center;">Status</th>
                        <th style="padding:0.75rem; text-align:center;">Kondisi</th>
                        <th style="padding:0.75rem; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($copies as $c): ?>
                        <tr>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-family:monospace; font-weight:700; font-size:0.875rem; color:var(--primary);">
                                    <?= htmlspecialchars($c['inventory_barcode'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php if (!empty($c['rfid_epc_hex'])): ?>
                                    <div style="font-size:0.6875rem; color:var(--muted-foreground); font-family:monospace;">
                                        RFID: <?= htmlspecialchars($c['rfid_epc_hex'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-weight:600; color:var(--foreground); margin-bottom:0.25rem;">
                                    <?= htmlspecialchars($c['title'] ?? 'Judul Tidak Diketahui', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                    Penulis: <?= htmlspecialchars($c['authors_text'] ?? '-', ENT_QUOTES, 'UTF-8') ?> &bull; No. Panggil: <?= htmlspecialchars($c['call_number_base'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-weight:500;">
                                    <?= htmlspecialchars($c['shelf_building'] ?? 'Gedung Utama', ENT_QUOTES, 'UTF-8') ?>, Lt. <?= (int)($c['shelf_floor'] ?? 1) ?>
                                </div>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                    Rak / Baris: <?= htmlspecialchars($c['shelf_aisle'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td style="padding:0.75rem; text-align:center; vertical-align:middle;">
                                <?php
                                $status = $c['availability_status'] ?? 'AVAILABLE';
                                $badgeColor = 'background-color:#dcfce7; color:#166534;';
                                if ($status === 'BORROWED') {
                                    $badgeColor = 'background-color:#dbeafe; color:#1e40af;';
                                } elseif ($status === 'RESERVED') {
                                    $badgeColor = 'background-color:#fef3c7; color:#92400e;';
                                } elseif ($status === 'MAINTENANCE') {
                                    $badgeColor = 'background-color:#f1f5f9; color:#475569;';
                                } elseif (in_array($status, ['DAMAGED', 'LOST'], true)) {
                                    $badgeColor = 'background-color:#fee2e2; color:#991b1b;';
                                }
                                ?>
                                <span style="display:inline-block; padding:0.2rem 0.5rem; border-radius:9999px; font-size:0.6875rem; font-weight:600; <?= $badgeColor ?>">
                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td style="padding:0.75rem; text-align:center; vertical-align:middle;">
                                <span style="font-size:0.8125rem; font-weight:500;">
                                    <?= htmlspecialchars($c['condition_status'] ?? 'GOOD', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td style="padding:0.75rem; text-align:right; vertical-align:middle; white-space:nowrap;">
                                <div style="display:inline-flex; gap:0.35rem;">
                                    <a href="/admin/copies/<?= (int)$c['id'] ?>/label" target="_blank" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.75rem;" title="Cetak Label Barcode & Punggung">Cetak Label</a>
                                    <a href="/admin/copies/<?= (int)$c['id'] ?>/edit" class="btn btn-outline" style="padding:0.25rem 0.5rem; font-size:0.75rem;">Edit</a>
                                    <?php if ($c['availability_status'] !== 'BORROWED'): ?>
                                        <form method="POST" action="/admin/copies/<?= (int)$c['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus eksemplar fisik ini?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="btn btn-danger" style="padding:0.25rem 0.5rem; font-size:0.75rem;">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div style="display:flex; justify-content:center; gap:0.5rem; margin-top:1.5rem;">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="/admin/copies?page=<?= $p ?>&q=<?= urlencode($filters['keyword'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? '') ?>&condition=<?= urlencode($filters['condition'] ?? '') ?>"
                       class="btn <?= $p === $currentPage ? 'btn-primary' : 'btn-outline' ?>"
                       style="padding:0.35rem 0.75rem; font-size:0.8125rem;">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
