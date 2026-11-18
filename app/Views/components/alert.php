<?php
declare(strict_types=1);

if (!empty($flashSuccess)): ?>
    <?php foreach ($flashSuccess as $msg): ?>
        <div class="alert alert-success" role="alert">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span><?= htmlspecialchars((string)$msg, ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="btn btn-ghost btn-sm" data-alert-close aria-label="Tutup">&times;</button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($flashError)): ?>
    <?php foreach ($flashError as $msg): ?>
        <div class="alert alert-error" role="alert">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span><?= htmlspecialchars((string)$msg, ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="btn btn-ghost btn-sm" data-alert-close aria-label="Tutup">&times;</button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($flashInfo)): ?>
    <?php foreach ($flashInfo as $msg): ?>
        <div class="alert alert-info" role="alert">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span><?= htmlspecialchars((string)$msg, ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="btn btn-ghost btn-sm" data-alert-close aria-label="Tutup">&times;</button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
