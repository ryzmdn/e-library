<?php
declare(strict_types=1);

$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? '/catalog';
$queryParams = $queryParams ?? [];

$buildPageUrl = function (int $p) use ($baseUrl, $queryParams): string {
    $params = $queryParams;
    $params['page'] = $p;
    return $baseUrl . '?' . http_build_query($params);
};
?>
<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="Navigasi Halaman" style="display:flex; justify-content:center; align-items:center; gap:0.5rem; margin-top:2.5rem;">
    <?php if ($currentPage > 1): ?>
        <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline btn-sm">
            Sebelumnya
        </a>
    <?php else: ?>
        <button type="button" class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">
            Sebelumnya
        </button>
    <?php endif; ?>

    <div style="display:flex; align-items:center; gap:0.25rem;">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === 1 || $p === $totalPages || abs($p - $currentPage) <= 2): ?>
                <?php if ($p === $currentPage): ?>
                    <span class="btn btn-primary btn-sm" style="min-width:2.25rem; text-align:center;">
                        <?= $p ?>
                    </span>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($buildPageUrl($p), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm" style="min-width:2.25rem; text-align:center;">
                        <?= $p ?>
                    </a>
                <?php endif; ?>
            <?php elseif ($p === 2 && $currentPage > 4): ?>
                <span style="padding:0 0.25rem; color:var(--muted-foreground);">&hellip;</span>
            <?php elseif ($p === $totalPages - 1 && $currentPage < $totalPages - 3): ?>
                <span style="padding:0 0.25rem; color:var(--muted-foreground);">&hellip;</span>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    <?php if ($currentPage < $totalPages): ?>
        <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline btn-sm">
            Berikutnya
        </a>
    <?php else: ?>
        <button type="button" class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">
            Berikutnya
        </button>
    <?php endif; ?>
</nav>
<?php endif; ?>
