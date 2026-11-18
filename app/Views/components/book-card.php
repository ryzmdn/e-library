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
<div class="card">
    <div class="card-header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.5rem;">
            <span class="badge"><?= htmlspecialchars((string)$year, ENT_QUOTES, 'UTF-8') ?></span>
            <?php if ($availableCopies > 0): ?>
                <span class="badge badge-available">Tersedia <?= $availableCopies ?> Eksemplar</span>
            <?php elseif ($mediaType === 'DIGITAL_ONLY'): ?>
                <span class="badge badge-available">E-Book Digital</span>
            <?php else: ?>
                <span class="badge badge-borrowed">Sedang Dipinjam</span>
            <?php endif; ?>
        </div>
        <h3 class="card-title" style="margin-top:0.5rem; font-size:1.1rem;">
            <a href="/book/<?= urlencode((string)$slug) ?>">
                <?= htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h3>
        <p class="card-description"><?= htmlspecialchars((string)$authors, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card-content">
        <div style="font-size:0.8125rem; color:var(--muted-foreground);">
            <span>No. Panggil: <?= htmlspecialchars((string)$callNumber, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>
    <div class="card-footer">
        <a href="/book/<?= urlencode((string)$slug) ?>" class="btn btn-outline btn-sm" style="width:100%;">Lihat Detail</a>
    </div>
</div>
