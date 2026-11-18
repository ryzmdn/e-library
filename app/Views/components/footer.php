<?php
declare(strict_types=1);
?>
<footer class="site-footer">
    <div class="container footer-content">
        <div>
            <p><?= htmlspecialchars($appName ?? 'E-Library', ENT_QUOTES, 'UTF-8') ?> &mdash; Sistem Perpustakaan Digital & Sirkulasi Terpadu.</p>
        </div>
        <div>
            <p>&copy; <?= date('Y') ?> Hak Cipta Dilindungi Undang-Undang.</p>
        </div>
    </div>
</footer>
