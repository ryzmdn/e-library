<?php
declare(strict_types=1);

$unpaidUsers = $unpaidUsers ?? [];
$totalUnpaidCount = $totalUnpaidCount ?? 0;
$recentPayments = $recentPayments ?? [];
?>
<section style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
        <div>
            <h1 style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.25rem;">
                Manajemen Kasir & Buku Besar Denda
            </h1>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">
                Pencatatan pelunasan denda keterlambatan buku, ganti rugi fisik, dan riwayat penerimaan kas atau QRIS.
            </p>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <a href="/admin/circulation" class="btn btn-outline btn-sm">Meja Sirkulasi</a>
        </div>
    </div>
</section>

<section style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start; margin-bottom:2.5rem;">
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="card-title" style="font-size:1.15rem;">Anggota Memiliki Tunggakan Denda</h2>
                <p class="card-description">Anggota yang tidak dapat meminjam atau reservasi hingga denda dilunasi.</p>
            </div>
            <span class="badge badge-borrowed"><?= $totalUnpaidCount ?> Anggota</span>
        </div>

        <div class="card-content" style="padding:0; overflow-x:auto;">
            <?php if (empty($unpaidUsers)): ?>
                <div style="padding:3rem 1.5rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                    Tidak ada anggota yang memiliki tunggakan denda saat ini.
                </div>
            <?php else: ?>
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                            <th style="padding:0.75rem 1rem;">Anggota</th>
                            <th style="padding:0.75rem 1rem;">Kontak</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Total Tertunggak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unpaidUsers as $u): ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:600;"><?= htmlspecialchars((string)$u['full_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div style="font-family:monospace; font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars((string)$u['member_number'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td style="padding:0.75rem 1rem; font-size:0.75rem; color:var(--muted-foreground);">
                                    <?= htmlspecialchars((string)($u['phone_number'] ?? $u['email']), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem; text-align:right;">
                                    <span style="font-weight:700; color:var(--destructive); font-size:0.9375rem;">
                                        Rp <?= number_format((float)$u['unpaid_total'], 2, ',', '.') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title" style="font-size:1.15rem;">Loket Pelunasan Denda</h2>
            <p class="card-description">Entri pembayaran tunai, QRIS, atau persetujuan pemutihan staf.</p>
        </div>
        <form method="POST" action="/admin/fines/pay" class="card-content" style="display:flex; flex-direction:column; gap:1.25rem;">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="pay_loan_id" class="label">ID Transaksi Peminjaman</label>
                <input
                    type="number"
                    id="pay_loan_id"
                    name="loan_id"
                    class="input"
                    placeholder="Masukkan ID pinjaman terkait"
                    required
                >
            </div>

            <div class="form-group">
                <label for="pay_user_id" class="label">ID Pengguna / Anggota</label>
                <input
                    type="number"
                    id="pay_user_id"
                    name="user_id"
                    class="input"
                    placeholder="Masukkan ID anggota"
                    required
                >
            </div>

            <div class="form-group">
                <label for="pay_amount" class="label">Nominal Pembayaran (Rp)</label>
                <input
                    type="number"
                    step="0.01"
                    id="pay_amount"
                    name="amount"
                    class="input"
                    placeholder="Contoh: 15000"
                    required
                >
            </div>

            <div class="form-group">
                <label for="payment_type" class="label">Metode Pembayaran</label>
                <select id="payment_type" name="payment_type" class="input">
                    <option value="PAYMENT_CASH" selected>Tunai di Meja Sirkulasi (CASH)</option>
                    <option value="PAYMENT_QRIS">Pembayaran Non-Tunai (QRIS / Transfer)</option>
                    <option value="WAIVER_ADMIN">Pemutihan Denda Kebijakan Staf (WAIVER)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="receipt_reference" class="label">Nomor Referensi / Struk (Opsional)</label>
                <input
                    type="text"
                    id="receipt_reference"
                    name="receipt_reference"
                    class="input"
                    placeholder="Contoh: QRIS-9912048 atau KAS-001"
                >
            </div>

            <div class="form-group">
                <label for="fine_notes" class="label">Catatan Transaksi</label>
                <input
                    type="text"
                    id="fine_notes"
                    name="notes"
                    class="input"
                    placeholder="Keterangan pendukung pelunasan"
                >
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Simpan Pelunasan Denda
                </button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title" style="font-size:1.15rem;">Penerimaan Pembayaran Terbaru</h2>
            <p class="card-description">Buku besar riwayat penerimaan kas dan pemutihan denda sirkulasi.</p>
        </div>
        <div class="card-content" style="padding:0; overflow-x:auto;">
            <?php if (empty($recentPayments)): ?>
                <div style="padding:2.5rem 1.5rem; text-align:center; color:var(--muted-foreground); font-size:0.875rem;">
                    Belum ada riwayat pembayaran denda yang tercatat.
                </div>
            <?php else: ?>
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background-color:var(--muted); text-align:left;">
                            <th style="padding:0.75rem 1rem;">Waktu</th>
                            <th style="padding:0.75rem 1rem;">Anggota</th>
                            <th style="padding:0.75rem 1rem;">Kode Pinjaman</th>
                            <th style="padding:0.75rem 1rem;">Metode</th>
                            <th style="padding:0.75rem 1rem;">Petugas</th>
                            <th style="padding:0.75rem 1rem;">No. Referensi</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPayments as $row): ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:0.75rem 1rem; font-size:0.75rem;">
                                    <?= date('d M Y, H:i', strtotime((string)$row['created_at'])) ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:600;"><?= htmlspecialchars((string)$row['member_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div style="font-family:monospace; font-size:0.75rem; color:var(--muted-foreground);"><?= htmlspecialchars((string)$row['member_number'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td style="padding:0.75rem 1rem; font-family:monospace; font-size:0.75rem;">
                                    <?= htmlspecialchars((string)($row['transaction_code'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?php $m = (string)$row['entry_type']; ?>
                                    <?php if ($m === 'PAYMENT_CASH'): ?>
                                        <span class="badge badge-outline">Tunai</span>
                                    <?php elseif ($m === 'PAYMENT_QRIS'): ?>
                                        <span class="badge badge-available">QRIS</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pemutihan</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem; font-size:0.75rem;">
                                    <?= htmlspecialchars((string)($row['staff_name'] ?? 'Sistem'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem; font-family:monospace; font-size:0.75rem;">
                                    <?= htmlspecialchars((string)($row['receipt_reference'] ?? '&mdash;'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding:0.75rem 1rem; text-align:right; font-weight:700; color:var(--primary);">
                                    Rp <?= number_format((float)$row['amount'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>
