<?php
declare(strict_types=1);
?>
<footer class="site-footer">
    <div class="container footer-content">
        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
            <a href="/" style="display:flex; align-items:center; gap:0.4rem; text-decoration:none; color:var(--foreground); font-weight:700;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary);">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                    <path d="M6 6h10"></path>
                    <path d="M6 10h10"></path>
                </svg>
                <span>RuangBaca</span>
            </a>
            <span style="color:var(--muted-foreground);">&bull;</span>
            <span style="color:var(--muted-foreground); font-size:0.8125rem;">Membaca lebih menyenangkan, mudah, dan tenang.</span>
        </div>

        <div class="footer-links">
            <a href="/catalog">Katalog</a>
            <a href="/catalog#panduan">Panduan Meminjam</a>
            <a href="/catalog#bantuan">Bantuan</a>
            <a href="/catalog#privasi">Kebijakan Privasi</a>
        </div>

        <div style="font-size:0.8125rem; color:var(--muted-foreground);">
            &copy; <?= date('Y') ?> RuangBaca E-Perpus. Hak Cipta Dilindungi.
        </div>
    </div>
</footer>
