<div style="margin-bottom:1.5rem;">
    <div style="font-size:0.875rem; margin-bottom:0.25rem;">
        <a href="/admin/members" style="color:var(--muted-foreground); text-decoration:none;">&larr; Kembali ke Data Anggota</a>
    </div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;"><?= htmlspecialchars($member['full_name'] ?? 'Anggota', ENT_QUOTES, 'UTF-8') ?></h1>
            <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Nomor Anggota: <strong style="color:var(--foreground); font-family:monospace;"><?= htmlspecialchars($member['member_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong> &bull; Peran: <?= htmlspecialchars($member['role'] ?? 'MEMBER', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <?php
            $status = $member['account_status'] ?? 'PENDING_VERIFICATION';
            $badgeStyle = 'background-color:#f1f5f9; color:#475569;';
            if ($status === 'ACTIVE') {
                $badgeStyle = 'background-color:#dcfce7; color:#166534;';
            } elseif ($status === 'PENDING_VERIFICATION') {
                $badgeStyle = 'background-color:#fef3c7; color:#92400e;';
            } elseif ($status === 'SUSPENDED') {
                $badgeStyle = 'background-color:#fee2e2; color:#991b1b;';
            } elseif ($status === 'GRADUATED_CLEAR') {
                $badgeStyle = 'background-color:#e0e7ff; color:#3730a3;';
            }
            ?>
            <span style="display:inline-block; padding:0.35rem 0.75rem; border-radius:9999px; font-size:0.8125rem; font-weight:600; <?= $badgeStyle ?>">
                STATUS: <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
    <div class="card" style="padding:1.5rem;">
        <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Profil & Akses Keanggotaan</h2>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; font-size:0.875rem; margin-bottom:1rem;">
            <div>
                <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Alamat Email:</span>
                <strong><?= htmlspecialchars($member['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <div>
                <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Nomor Telepon / WA:</span>
                <strong><?= htmlspecialchars($member['phone_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <div>
                <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Masa Berlaku Kartu:</span>
                <strong><?= htmlspecialchars($member['membership_expires_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <div>
                <span style="color:var(--muted-foreground); display:block; font-size:0.75rem;">Sisa Tagihan Denda:</span>
                <strong style="color:<?= $unpaidBalance > 0 ? '#b91c1c' : 'inherit' ?>;">Rp <?= number_format($unpaidBalance, 0, ',', '.') ?></strong>
            </div>
        </div>

        <div style="border-top:1px solid var(--border); padding-top:1rem; margin-top:1rem;">
            <h3 style="font-size:0.875rem; font-weight:600; margin:0 0 0.5rem 0;">Akses Dokumen Digital Terproteksi:</h3>
            <div style="font-size:0.8125rem; color:var(--muted-foreground);">
                Streaming E-Book: <strong style="color:var(--foreground);"><?= !empty($member['can_access_digital']) ? 'Diizinkan' : 'Dibatasi' ?></strong> &bull;
                Skripsi Penuh: <strong style="color:var(--foreground);"><?= !empty($member['can_access_restricted_thesis']) ? 'Diizinkan' : 'Dibatasi' ?></strong>
            </div>
        </div>
    </div>

    <div class="card" style="padding:1.5rem;">
        <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Tindakan Staf Perpustakaan</h2>

        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            <form method="POST" action="/admin/members/<?= (int)$member['id'] ?>/status" style="display:flex; gap:0.5rem; align-items:flex-end;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div style="flex:1;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; margin-bottom:0.25rem;">Ubah Status Akun:</label>
                    <select name="account_status" class="form-control" style="font-size:0.8125rem;">
                        <option value="ACTIVE" <?= $status === 'ACTIVE' ? 'selected' : '' ?>>Aktifkan (ACTIVE)</option>
                        <option value="PENDING_VERIFICATION" <?= $status === 'PENDING_VERIFICATION' ? 'selected' : '' ?>>Tangguhkan / Menunggu Verifikasi</option>
                        <option value="SUSPENDED" <?= $status === 'SUSPENDED' ? 'selected' : '' ?>>Blokir / Tangguhkan (SUSPENDED)</option>
                        <option value="GRADUATED_CLEAR" <?= $status === 'GRADUATED_CLEAR' ? 'selected' : '' ?>>Bebas Pustaka / Lulus</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="font-size:0.8125rem;">Simpan</button>
            </form>

            <form method="POST" action="/admin/members/<?= (int)$member['id'] ?>/tier" style="display:flex; gap:0.5rem; align-items:flex-end;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div style="flex:1;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; margin-bottom:0.25rem;">Tingkat Keanggotaan (Tier):</label>
                    <select name="tier_id" class="form-control" style="font-size:0.8125rem;">
                        <option value="1" <?= (int)($member['tier_id'] ?? 1) === 1 ? 'selected' : '' ?>>Reguler / Mahasiswa (3 Buku, 14 Hari)</option>
                        <option value="2" <?= (int)($member['tier_id'] ?? 1) === 2 ? 'selected' : '' ?>>Dosen / Peneliti (7 Buku, 30 Hari)</option>
                        <option value="3" <?= (int)($member['tier_id'] ?? 1) === 3 ? 'selected' : '' ?>>Pascasarjana (5 Buku, 21 Hari)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline" style="font-size:0.8125rem;">Ubah</button>
            </form>

            <form method="POST" action="/admin/members/<?= (int)$member['id'] ?>/extend" style="display:flex; gap:0.5rem; align-items:flex-end;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div style="flex:1;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; margin-bottom:0.25rem;">Perpanjang Masa Aktif Kartu:</label>
                    <select name="months" class="form-control" style="font-size:0.8125rem;">
                        <option value="6">+ 6 Bulan</option>
                        <option value="12" selected>+ 1 Tahun (12 Bulan)</option>
                        <option value="24">+ 2 Tahun (24 Bulan)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline" style="font-size:0.8125rem;">Perpanjang</button>
            </form>
        </div>
    </div>
</div>

<div class="card" style="padding:1.5rem; margin-bottom:2rem;">
    <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0;">Pinjaman Buku Fisik Aktif</h2>

    <?php if (empty($activeLoans)): ?>
        <div style="text-align:center; padding:2rem; color:var(--muted-foreground); font-size:0.875rem;">
            Anggota ini sedang tidak meminjam buku fisik apapun.
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; font-size:0.875rem;">
                <thead>
                    <tr>
                        <th style="padding:0.5rem;">Kode Transaksi</th>
                        <th style="padding:0.5rem;">Barcode Eksemplar</th>
                        <th style="padding:0.5rem;">Judul Buku</th>
                        <th style="padding:0.5rem;">Tanggal Pinjam</th>
                        <th style="padding:0.5rem;">Jatuh Tempo</th>
                        <th style="padding:0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeLoans as $al): ?>
                        <tr>
                            <td style="padding:0.5rem; font-family:monospace;"><?= htmlspecialchars($al['transaction_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem; font-family:monospace; font-weight:600;"><?= htmlspecialchars($al['inventory_barcode'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem;"><?= htmlspecialchars($al['title'] ?? 'Buku', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem;"><?= htmlspecialchars(substr($al['borrowed_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem;"><?= htmlspecialchars($al['due_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem;">
                                <?php
                                $ls = $al['loan_status'] ?? 'ACTIVE';
                                $lcolor = $ls === 'OVERDUE' ? 'background-color:#fee2e2; color:#991b1b;' : 'background-color:#dbeafe; color:#1e40af;';
                                ?>
                                <span style="display:inline-block; padding:0.125rem 0.5rem; border-radius:9999px; font-size:0.6875rem; font-weight:600; <?= $lcolor ?>">
                                    <?= htmlspecialchars($ls, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card" style="padding:1.5rem;">
    <h2 style="font-size:1.125rem; font-weight:600; margin:0 0 1rem 0;">Buku Besar Denda & Pembayaran Terakhir</h2>

    <?php if (empty($fines)): ?>
        <div style="text-align:center; padding:2rem; color:var(--muted-foreground); font-size:0.875rem;">
            Tidak ada riwayat denda atau pembayaran denda untuk anggota ini.
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; font-size:0.8125rem;">
                <thead>
                    <tr>
                        <th style="padding:0.5rem;">Waktu</th>
                        <th style="padding:0.5rem;">Transaksi Terkait</th>
                        <th style="padding:0.5rem;">Jenis Entri</th>
                        <th style="padding:0.5rem; text-align:right;">Nominal (Rp)</th>
                        <th style="padding:0.5rem;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fines as $f): ?>
                        <tr>
                            <td style="padding:0.5rem; white-space:nowrap;"><?= htmlspecialchars($f['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem; font-family:monospace;"><?= htmlspecialchars($f['transaction_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="padding:0.5rem;">
                                <?php
                                $et = $f['entry_type'] ?? '';
                                $isPayment = in_array($et, ['PAYMENT_CASH', 'PAYMENT_QRIS', 'WAIVER_ADMIN'], true);
                                ?>
                                <span style="font-weight:600; color:<?= $isPayment ? '#166534' : '#991b1b' ?>;">
                                    <?= htmlspecialchars($et, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td style="padding:0.5rem; text-align:right; font-weight:600;">
                                <?= number_format((float)($f['amount'] ?? 0), 0, ',', '.') ?>
                            </td>
                            <td style="padding:0.5rem;"><?= htmlspecialchars($f['notes'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
