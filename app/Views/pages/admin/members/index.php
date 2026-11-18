<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; margin:0 0 0.25rem 0;">Manajemen Data Anggota</h1>
        <p style="color:var(--muted-foreground); margin:0; font-size:0.875rem;">Kelola data anggota, verifikasi registrasi mandiri, pembaharuan tier, dan kontrol suspensi akun.</p>
    </div>
</div>

<div class="card" style="padding:1.25rem; margin-bottom:1.5rem;">
    <form method="GET" action="/admin/members" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
        <div style="flex:1; min-width:240px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Pencarian Nama / No. Anggota / Email / HP</label>
            <input type="text" name="q" value="<?= htmlspecialchars($filters['keyword'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ketik kata kunci pencarian..." class="form-control">
        </div>

        <div style="width:160px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Peran (Role)</label>
            <select name="role" class="form-control">
                <option value="">Semua Peran</option>
                <option value="MEMBER" <?= ($filters['role'] ?? '') === 'MEMBER' ? 'selected' : '' ?>>Anggota (MEMBER)</option>
                <option value="LIBRARIAN" <?= ($filters['role'] ?? '') === 'LIBRARIAN' ? 'selected' : '' ?>>Pustakawan (LIBRARIAN)</option>
                <option value="CATALOGER" <?= ($filters['role'] ?? '') === 'CATALOGER' ? 'selected' : '' ?>>Kataloger (CATALOGER)</option>
                <option value="SUPER_ADMIN" <?= ($filters['role'] ?? '') === 'SUPER_ADMIN' ? 'selected' : '' ?>>Super Admin</option>
            </select>
        </div>

        <div style="width:180px;">
            <label style="display:block; font-size:0.8125rem; font-weight:500; margin-bottom:0.35rem;">Status Akun</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="ACTIVE" <?= ($filters['status'] ?? '') === 'ACTIVE' ? 'selected' : '' ?>>Aktif (ACTIVE)</option>
                <option value="PENDING_VERIFICATION" <?= ($filters['status'] ?? '') === 'PENDING_VERIFICATION' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                <option value="SUSPENDED" <?= ($filters['status'] ?? '') === 'SUSPENDED' ? 'selected' : '' ?>>Ditangguhkan (SUSPENDED)</option>
                <option value="GRADUATED_CLEAR" <?= ($filters['status'] ?? '') === 'GRADUATED_CLEAR' ? 'selected' : '' ?>>Bebas Pustaka (Lulus)</option>
            </select>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="/admin/members" class="btn btn-outline">Reset</a>
        </div>
    </form>
</div>

<div class="card" style="padding:1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <div style="font-size:0.875rem; color:var(--muted-foreground);">
            Menampilkan <?= count($users) ?> dari <?= number_format($totalUsers, 0, ',', '.') ?> pengguna terdaftar
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div style="text-align:center; padding:3rem 1rem; color:var(--muted-foreground);">
            <div style="font-weight:600; font-size:1rem; margin-bottom:0.5rem;">Tidak ada pengguna ditemukan</div>
            <p style="font-size:0.875rem; margin:0;">Coba sesuaikan filter pencarian pengguna perpustakaan.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; font-size:0.875rem;">
                <thead>
                    <tr>
                        <th style="padding:0.75rem;">No. Anggota</th>
                        <th style="padding:0.75rem;">Nama Lengkap & Kontak</th>
                        <th style="padding:0.75rem;">Peran & Tingkat (Tier)</th>
                        <th style="padding:0.75rem;">Masa Aktif</th>
                        <th style="padding:0.75rem; text-align:center;">Status Akun</th>
                        <th style="padding:0.75rem; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-family:monospace; font-weight:700; color:var(--primary);">
                                    <?= htmlspecialchars($u['member_number'] ?? 'GUEST', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div style="font-size:0.6875rem; color:var(--muted-foreground);">
                                    ID: #<?= (int)$u['id'] ?>
                                </div>
                            </td>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-weight:600; color:var(--foreground);">
                                    <?= htmlspecialchars($u['full_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                    <?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($u['phone_number'])): ?>
                                        &bull; <?= htmlspecialchars($u['phone_number'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding:0.75rem; vertical-align:middle;">
                                <div style="font-weight:500; font-size:0.8125rem;">
                                    <?= htmlspecialchars($u['role'] ?? 'MEMBER', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);">
                                    <?= htmlspecialchars($u['tier_name'] ?? 'Reguler', ENT_QUOTES, 'UTF-8') ?> (Maks. <?= (int)($u['max_physical_loans'] ?? 3) ?> buku)
                                </div>
                            </td>
                            <td style="padding:0.75rem; vertical-align:middle; white-space:nowrap; font-size:0.8125rem;">
                                <?= htmlspecialchars($u['membership_expires_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td style="padding:0.75rem; text-align:center; vertical-align:middle;">
                                <?php
                                $status = $u['account_status'] ?? 'PENDING_VERIFICATION';
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
                                <span style="display:inline-block; padding:0.2rem 0.5rem; border-radius:9999px; font-size:0.6875rem; font-weight:600; <?= $badgeStyle ?>">
                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td style="padding:0.75rem; text-align:right; vertical-align:middle; white-space:nowrap;">
                                <a href="/admin/members/<?= (int)$u['id'] ?>" class="btn btn-outline" style="padding:0.25rem 0.6rem; font-size:0.75rem;">
                                    Kelola Anggota &rarr;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div style="display:flex; justify-content:center; gap:0.5rem; margin-top:1.5rem;">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="/admin/members?page=<?= $p ?>&q=<?= urlencode($filters['keyword'] ?? '') ?>&role=<?= urlencode($filters['role'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? '') ?>"
                       class="btn <?= $p === $currentPage ? 'btn-primary' : 'btn-outline' ?>"
                       style="padding:0.35rem 0.75rem; font-size:0.8125rem;">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
