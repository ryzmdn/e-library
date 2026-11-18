<?php
declare(strict_types=1);
?>
<div>
    <h2 style="font-size:1.5rem; font-weight:700; letter-spacing:-0.03em; margin-bottom:0.35rem; text-align:center;">
        Masuk ke Sistem
    </h2>
    <p style="font-size:0.875rem; color:var(--muted-foreground); margin-bottom:1.5rem; text-align:center;">
        Gunakan alamat email atau nomor anggota resmi perpustakaan Anda.
    </p>

    <form action="/login" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
            <label for="identity" class="label">Email atau Nomor Anggota</label>
            <input
                type="text"
                id="identity"
                name="identity"
                class="input"
                placeholder="nama@email.com atau ADM-001"
                value="<?= htmlspecialchars((string)($oldIdentity ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <label for="password" class="label">Kata Sandi</label>
            </div>
            <input
                type="password"
                id="password"
                name="password"
                class="input"
                placeholder="Masukkan kata sandi akun"
                required
            >
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary" style="width:100%;">
                Masuk ke Akun
            </button>
        </div>
    </form>

    <div style="margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--border); text-align:center; font-size:0.875rem;">
        <span style="color:var(--muted-foreground);">Belum terdaftar sebagai anggota?</span>
        <a href="/register" style="font-weight:600; text-decoration:underline; margin-left:0.35rem;">
            Daftar Sekarang
        </a>
    </div>

    <div style="margin-top:1rem; text-align:center;">
        <a href="/" style="font-size:0.8125rem; color:var(--muted-foreground);">
            &larr; Kembali ke Beranda Utama
        </a>
    </div>
</div>
