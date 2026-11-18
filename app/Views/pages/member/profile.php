<?php
declare(strict_types=1);

$member = $member ?? [];
$themePref = strtoupper((string)($member['theme_preference'] ?? 'LIGHT'));
?>
<section style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
        <div>
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
                <span class="badge badge-outline"><?= htmlspecialchars((string)($member['member_number'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                <span class="badge"><?= htmlspecialchars((string)($member['tier_name'] ?? 'Mahasiswa Reguler'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <h1 style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em;">
                Pengaturan Profil & Keamanan
            </h1>
            <p style="font-size:0.875rem; color:var(--muted-foreground);">
                Kelola informasi identitas, preferensi antarmuka, dan keamanan akun Anda.
            </p>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <a href="/member/dashboard" class="btn btn-outline btn-sm">Kembali ke Dasbor</a>
        </div>
    </div>
</section>

<form method="POST" action="/member/profile" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start;">
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(\App\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Informasi Pribadi</h2>
            <p class="card-description">Perbarui data kontak dan personalisasi pengalaman membaca Anda.</p>
        </div>
        <div class="card-content" style="display:flex; flex-direction:column; gap:1.25rem;">
            <div class="form-group">
                <label class="form-label" for="member_number">Nomor Anggota Perpustakaan</label>
                <input type="text" id="member_number" class="form-input" value="<?= htmlspecialchars((string)($member['member_number'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>" disabled readonly style="background-color:var(--muted); cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Alamat Surel (Email)</label>
                <input type="email" id="email" class="form-input" value="<?= htmlspecialchars((string)($member['email'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>" disabled readonly style="background-color:var(--muted); cursor:not-allowed;">
                <span style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Alamat surel terhubung langsung dengan validasi SSO dan verifikasi peminjaman.</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" class="form-input" value="<?= htmlspecialchars((string)($member['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone_number">Nomor Telepon / WhatsApp</label>
                <input type="tel" id="phone_number" name="phone_number" class="form-input" value="<?= htmlspecialchars((string)($member['phone_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="08xxxxxxxxxx">
            </div>

            <div class="form-group">
                <label class="form-label" for="theme_preference">Tema Tampilan Antarmuka</label>
                <select id="theme_preference" name="theme_preference" class="form-input">
                    <option value="LIGHT" <?= $themePref === 'LIGHT' ? 'selected' : '' ?>>Mode Terang (Light Mode)</option>
                    <option value="DARK" <?= $themePref === 'DARK' ? 'selected' : '' ?>>Mode Gelap (Dark Mode)</option>
                    <option value="SEPIA" <?= $themePref === 'SEPIA' ? 'selected' : '' ?>>Mode Sepia (Eye Comfort)</option>
                </select>
                <span style="font-size:0.75rem; color:var(--muted-foreground); margin-top:0.25rem;">Preferensi ini akan otomatis diselaraskan setiap kali Anda masuk ke sistem.</span>
            </div>
        </div>
        <div class="card-footer" style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">Simpan Profil</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Keamanan & Kata Sandi</h2>
            <p class="card-description">Kosongkan bagian ini jika Anda tidak ingin mengubah kata sandi akun.</p>
        </div>
        <div class="card-content" style="display:flex; flex-direction:column; gap:1.25rem;">
            <div class="form-group">
                <label class="form-label" for="current_password">Kata Sandi Saat Ini</label>
                <input type="password" id="current_password" name="current_password" class="form-input" autocomplete="current-password" placeholder="Masukkan kata sandi lama">
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">Kata Sandi Baru</label>
                <input type="password" id="new_password" name="new_password" class="form-input" autocomplete="new-password" placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password_confirmation">Konfirmasi Kata Sandi Baru</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-input" autocomplete="new-password" placeholder="Ulangi kata sandi baru">
            </div>

            <div style="padding:0.875rem; border:1px solid var(--border); border-radius:var(--radius); background-color:var(--muted); font-size:0.8125rem; color:var(--muted-foreground);">
                <div style="font-weight:600; color:var(--foreground); margin-bottom:0.25rem;">Pedoman Keamanan Sandi</div>
                Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol untuk menjaga keamanan akun Anda dari akses yang tidak sah.
            </div>
        </div>
        <div class="card-footer" style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">Perbarui Kata Sandi</button>
        </div>
    </div>
</form>
