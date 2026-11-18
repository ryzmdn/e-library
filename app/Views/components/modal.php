<?php
declare(strict_types=1);

$modalId = $modalId ?? 'default-modal';
$modalTitle = $modalTitle ?? 'Pemberitahuan';
$modalContent = $modalContent ?? '';
?>
<div class="modal-backdrop" id="<?= htmlspecialchars($modalId, ENT_QUOTES, 'UTF-8') ?>" role="dialog" aria-modal="true">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="card-title"><?= htmlspecialchars($modalTitle, ENT_QUOTES, 'UTF-8') ?></h3>
            <button type="button" class="btn btn-ghost btn-sm" data-modal-close aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-body">
            <?= $modalContent ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline btn-sm" data-modal-close>Tutup</button>
        </div>
    </div>
</div>
