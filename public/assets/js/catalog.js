document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('catalog-search-input');
    const suggestDropdown = document.getElementById('catalog-suggest-dropdown');

    if (!searchInput || !suggestDropdown) {
        return;
    }

    let debounceTimer = null;
    let selectedIndex = -1;

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const query = searchInput.value.trim();

        if (query.length < 2) {
            hideDropdown();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 250);
    });

    searchInput.addEventListener('keydown', (e) => {
        const items = suggestDropdown.querySelectorAll('.catalog-suggest-item');
        if (!items.length || suggestDropdown.style.display === 'none') {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateSelection(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            items[selectedIndex].click();
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestDropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    function fetchSuggestions(query) {
        fetch(`/api/catalog/suggest?q=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network error');
            }
            return response.json();
        })
        .then(data => {
            renderSuggestions(data.results || []);
        })
        .catch(() => {
            hideDropdown();
        });
    }

    function renderSuggestions(results) {
        if (!results.length) {
            hideDropdown();
            return;
        }

        selectedIndex = -1;
        suggestDropdown.innerHTML = '';

        results.forEach(item => {
            const row = document.createElement('a');
            row.href = item.url;
            row.className = 'catalog-suggest-item';
            row.style.display = 'flex';
            row.style.justifyContent = 'space-between';
            row.style.alignItems = 'center';
            row.style.padding = '0.75rem 1rem';
            row.style.borderBottom = '1px solid var(--border)';
            row.style.textDecoration = 'none';
            row.style.color = 'inherit';
            row.style.cursor = 'pointer';

            row.addEventListener('mouseenter', () => {
                row.style.backgroundColor = 'var(--muted)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.backgroundColor = 'transparent';
            });

            const infoDiv = document.createElement('div');
            infoDiv.style.flex = '1';
            infoDiv.style.marginRight = '0.75rem';

            const titleSpan = document.createElement('div');
            titleSpan.style.fontWeight = '600';
            titleSpan.style.fontSize = '0.875rem';
            titleSpan.textContent = item.title;

            const authorSpan = document.createElement('div');
            authorSpan.style.fontSize = '0.75rem';
            authorSpan.style.color = 'var(--muted-foreground)';
            authorSpan.textContent = `${item.authors} (${item.year})`;

            infoDiv.appendChild(titleSpan);
            infoDiv.appendChild(authorSpan);

            const badgeSpan = document.createElement('span');
            badgeSpan.className = 'badge';
            badgeSpan.style.fontSize = '0.7rem';
            if (item.available_copies > 0) {
                badgeSpan.className = 'badge badge-available';
                badgeSpan.textContent = `Tersedia ${item.available_copies}`;
            } else if (item.media_type === 'DIGITAL_ONLY') {
                badgeSpan.className = 'badge badge-available';
                badgeSpan.textContent = 'E-Book';
            } else {
                badgeSpan.className = 'badge badge-borrowed';
                badgeSpan.textContent = 'Dipinjam';
            }

            row.appendChild(infoDiv);
            row.appendChild(badgeSpan);
            suggestDropdown.appendChild(row);
        });

        suggestDropdown.style.display = 'block';
    }

    function updateSelection(items) {
        items.forEach((item, idx) => {
            if (idx === selectedIndex) {
                item.style.backgroundColor = 'var(--muted)';
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.style.backgroundColor = 'transparent';
            }
        });
    }

    function hideDropdown() {
        suggestDropdown.style.display = 'none';
        suggestDropdown.innerHTML = '';
        selectedIndex = -1;
    }
});
