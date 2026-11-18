<?php
declare(strict_types=1);

$book = $book ?? [];
$slug = $book['slug'] ?? '';
$title = $book['title'] ?? 'Judul Buku';
$authors = $book['authors_text'] ?? 'Penulis';
$callNumber = $book['call_number_base'] ?? '-';
$year = $book['publication_year'] ?? '-';
$availableCopies = (int)($book['available_physical_copies'] ?? 0);
$mediaType = $book['media_type'] ?? 'PHYSICAL_ONLY';
?>
<div class="card" style="display:flex; flex-direction:column; justify-content:space-between;">
    <div class="card-header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.5rem; margin-bottom:0.5rem;">
            <span class="badge"><?= htmlspecialchars((string)$year, ENT_QUOTES, 'UTF-8') ?></span>
            <?php if ($availableCopies > 0): ?>
                <span class="badge badge-available">Tersedia <?= $availableCopies ?> Eksemplar</span>
            <?php elseif ($mediaType === 'DIGITAL_ONLY'): ?>
                <span class="badge badge-available">E-Book Digital</span>
            <?php else: ?>
                <span class="badge badge-borrowed">Sedang Dipinjam</span>
            <?php endif; ?>
        </div>
        <h3 class="card-title" style="font-size:1.05rem; line-height:1.35;">
            <a href="/catalog/<?= urlencode((string)$slug) ?>" style="color:inherit; text-decoration:none;">
                <?= htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h3>
        <p class="card-description" style="font-size:0.8125rem; margin-top:0.25rem;">
            <?= htmlspecialchars((string)$authors, ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
    <div class="card-content" style="padding-top:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; color:var(--muted-foreground); border-top:1px solid var(--border); padding-top:0.625rem; margin-top:0.5rem;">
            <span>No. Panggil: <?= htmlspecialchars((string)$callNumber, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="badge badge-outline" style="font-size:0.7rem;"><?= htmlspecialchars((string)$mediaType, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>
    <div class="card-footer" style="padding-top:0.5rem;">
        <a href="/catalog/<?= urlencode((string)$slug) ?>" class="btn btn-outline btn-sm" style="width:100%; text-align:center;">
            Lihat Ketersediaan & Detail
        </a>
    </div>
</div>
