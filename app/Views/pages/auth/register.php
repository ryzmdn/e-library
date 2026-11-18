<?php
declare(strict_types=1);
?>
<div>
    <h2 style="font-size:1.5rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.35rem; text-align:center;">
        Pendaftaran Anggota
    </h2>
    <p style="font-size:0.875rem; color:var(--muted-foreground); margin-bottom:1.5rem; text-align:center;">
        Daftarkan diri Anda untuk mengakses peminjaman fisik dan koleksi digital.
    </p>

    <form action="/register" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
            <label for="full_name" class="label">Nama Lengkap</label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                class="input"
                placeholder="Nama lengkap sesuai kartu identitas"
                value="<?= htmlspecialchars((string)($oldName ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="email" class="label">Alamat Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="input"
                placeholder="nama@email.com"
                value="<?= htmlspecialchars((string)($oldEmail ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                required
            >
            <span class="form-hint">Email akan digunakan sebagai salah satu identitas saat masuk.</span>
        </div>

        <div class="form-group">
            <label for="phone_number" class="label">Nomor Telepon</label>
            <input
                type="tel"
                id="phone_number"
                name="phone_number"
                class="input"
                placeholder="081234567890"
                value="<?= htmlspecialchars((string)($oldPhone ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="form-group">
            <label for="password" class="label">Kata Sandi Baru</label>
            <input
                type="password"
                id="password"
                name="password"
                class="input"
                placeholder="Minimal 6 karakter"
                required
            >
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="label">Ulangi Kata Sandi Baru</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="input"
                placeholder="Ketik ulang kata sandi yang sama"
                required
            >
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary" style="width:100%;">
                Daftar Sebagai Anggota
            </button>
        </div>
    </form>

    <div style="margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--border); text-align:center; font-size:0.875rem;">
        <span style="color:var(--muted-foreground);">Sudah terdaftar sebagai anggota?</span>
        <a href="/login" style="font-weight:600; text-decoration:underline; margin-left:0.35rem;">
            Masuk di Sini
        </a>
    </div>

    <div style="margin-top:1rem; text-align:center;">
        <a href="/" style="font-size:0.8125rem; color:var(--muted-foreground);">
            &larr; Kembali ke Beranda Utama
        </a>
    </div>
</div>
