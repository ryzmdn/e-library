<?php
declare(strict_types=1);

$statTitle = $statTitle ?? 'Statistik';
$statValue = $statValue ?? '0';
$statDescription = $statDescription ?? '';
?>
<div class="card">
    <div class="card-header" style="padding-bottom:0.25rem;">
        <span class="card-description"><?= htmlspecialchars((string)$statTitle, ENT_QUOTES, 'UTF-8') ?></span>
        <div style="font-size:1.75rem; font-weight:700; letter-spacing:-0.03em; margin-top:0.25rem;">
            <?= htmlspecialchars((string)$statValue, ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
    <?php if ($statDescription !== ''): ?>
        <div class="card-content" style="padding-top:0; font-size:0.8125rem; color:var(--muted-foreground);">
            <?= htmlspecialchars((string)$statDescription, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
</div>
