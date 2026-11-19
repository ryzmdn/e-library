<?php
declare(strict_types=1);
?>
<div id="quick-search-modal" class="modal-backdrop" aria-hidden="true">
    <div class="modal-content" style="max-width:560px;">
        <div style="display:flex; align-items:center; gap:0.75rem; padding:1rem 1.25rem; border-bottom:1px solid var(--border-subtle);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--muted-foreground); flex-shrink:0;">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <input type="text" id="quick-search-input" placeholder="Ketik judul buku, nama penulis, ISBN, atau topik..." style="flex:1; border:none; background:transparent; font-size:0.9375rem; color:var(--foreground); outline:none;">
            <button type="button" data-modal-close class="btn btn-ghost btn-sm" style="padding:0.25rem 0.5rem; font-size:0.75rem;">ESC</button>
        </div>

        <div id="quick-search-results" style="max-height:360px; overflow-y:auto; padding:0.5rem;">
            <div style="padding:0.75rem 0.75rem 0.5rem 0.75rem; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted-foreground);">
                Pencarian Terpopuler
            </div>
            <div style="display:flex; flex-direction:column; gap:0.25rem;">
                <a href="/catalog?q=Filosofi+Teras" class="dropdown-item" style="border-radius:var(--radius-md); padding:0.55rem 0.75rem;">
                    <span style="color:var(--primary); font-weight:600;">Filosofi Teras</span>
                    <span style="color:var(--muted-foreground); font-size:0.75rem; margin-left:auto;">Filsafat &amp; Self-Help</span>
                </a>
                <a href="/catalog?q=Atomic+Habits" class="dropdown-item" style="border-radius:var(--radius-md); padding:0.55rem 0.75rem;">
                    <span style="color:var(--primary); font-weight:600;">Atomic Habits</span>
                    <span style="color:var(--muted-foreground); font-size:0.75rem; margin-left:auto;">Produktivitas</span>
                </a>
                <a href="/catalog?q=Laut+Bercerita" class="dropdown-item" style="border-radius:var(--radius-md); padding:0.55rem 0.75rem;">
                    <span style="color:var(--primary); font-weight:600;">Laut Bercerita</span>
                    <span style="color:var(--muted-foreground); font-size:0.75rem; margin-left:auto;">Fiksi Sastra</span>
                </a>
                <a href="/catalog?q=Sapiens" class="dropdown-item" style="border-radius:var(--radius-md); padding:0.55rem 0.75rem;">
                    <span style="color:var(--primary); font-weight:600;">Sapiens: Riwayat Singkat Umat Manusia</span>
                    <span style="color:var(--muted-foreground); font-size:0.75rem; margin-left:auto;">Sejarah</span>
                </a>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.6rem 1.25rem; background-color:var(--secondary); border-top:1px solid var(--border-subtle); font-size:0.75rem; color:var(--muted-foreground);">
            <div>Gunakan kata kunci untuk mencari di ribuan koleksi</div>
            <div style="display:flex; gap:0.75rem;">
                <span><kbd class="search-shortcut-badge">&uarr;&darr;</kbd> Navigasi</span>
                <span><kbd class="search-shortcut-badge">Enter</kbd> Pilih</span>
            </div>
        </div>
    </div>
</div>
