<div class="no-print" style="margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; width:100%; max-width:600px;">
    <div>
        <div style="font-weight:600; font-size:1rem;">Pratinjau Label Inventaris</div>
        <div style="font-size:0.8125rem; color:var(--muted-foreground);">Siap dicetak pada stiker barcode / label punggung buku.</div>
    </div>
    <div style="display:flex; gap:0.5rem;">
        <button type="button" onclick="window.print()" class="btn btn-primary" style="font-size:0.875rem;">Cetak Label</button>
        <button type="button" onclick="window.close()" class="btn btn-outline" style="font-size:0.875rem;">Tutup</button>
    </div>
</div>

<div class="label-sheet" style="display:flex; gap:1.5rem; flex-wrap:wrap; background-color:#ffffff; padding:1.5rem; border:1px solid #cbd5e1; border-radius:6px; max-width:600px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div class="spine-label" style="width:140px; border:2px dashed #000000; padding:0.75rem; text-align:center; font-family:monospace; display:flex; flex-direction:column; justify-content:center;">
        <div style="font-size:0.6875rem; font-weight:700; text-transform:uppercase; border-bottom:1px solid #000; padding-bottom:0.25rem; margin-bottom:0.5rem;">
            PERPUSTAKAAN
        </div>
        <div style="font-size:1rem; font-weight:700; line-height:1.3; margin-bottom:0.25rem;">
            <?= htmlspecialchars($callNumber, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div style="font-size:0.75rem; margin-top:0.25rem; border-top:1px solid #000; padding-top:0.25rem;">
            c.<?= (int)($copy['id'] ?? 1) ?>
        </div>
    </div>

    <div class="barcode-label" style="flex:1; min-width:240px; border:2px dashed #000000; padding:0.75rem; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
        <div style="font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">
            PERPUSTAKAAN DIGITAL SISTEM
        </div>
        <div style="font-size:0.75rem; font-weight:600; max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:0.5rem;">
            <?= htmlspecialchars($copy['title'] ?? 'Koleksi Perpustakaan', ENT_QUOTES, 'UTF-8') ?>
        </div>

        <div style="margin:0.25rem 0;">
            <?= $barcodeSvg ?>
        </div>

        <div style="font-size:0.6875rem; color:#475569; margin-top:0.25rem;">
            Lokasi: Lt. <?= (int)($copy['shelf_floor'] ?? 1) ?> - <?= htmlspecialchars($copy['shelf_aisle'] ?? 'Rak Utama', ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
</div>
