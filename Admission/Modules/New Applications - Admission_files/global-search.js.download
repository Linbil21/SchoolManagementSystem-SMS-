document.addEventListener('DOMContentLoaded', function () {
    const searchBoxes = document.querySelectorAll('.search-box, .search-bar');

    // Inject Styles once
    const style = document.createElement('style');
    style.innerHTML = `
        .search-results-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            margin-top: 5px;
            overflow: hidden;
            display: none;
            max-height: 400px;
            overflow-y: auto;
        }

        .search-results-dropdown.active {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }

        .search-result-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: 0.2s;
            text-decoration: none;
            color: #1e293b;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: #f8fafc;
        }

        .search-result-icon {
            width: 32px;
            height: 32px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .search-result-content {
            flex: 1;
            overflow: hidden;
        }

        .search-result-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-result-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .no-results {
            padding: 15px;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);

    searchBoxes.forEach(box => {
        const input = box.querySelector('input');
        if (!input) return;

        // Create Dropdown Element
        const dropdown = document.createElement('div');
        dropdown.className = 'search-results-dropdown';
        box.appendChild(dropdown);

        let debounceTimer;

        input.addEventListener('input', function (e) {
            const query = e.target.value.trim();

            clearTimeout(debounceTimer);

            if (query.length < 2) {
                dropdown.classList.remove('active');
                dropdown.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                // Determine base path dynamically or use provided config
                const root = window.smsRoot || '/sms/';
                const apiPath = root + 'api/global_search.php?q=' + encodeURIComponent(query);

                dropdown.innerHTML = '<div class="no-results"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
                dropdown.classList.add('active');

                fetch(apiPath)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';

                        if (data.length === 0) {
                            dropdown.innerHTML = '<div class="no-results">No results found for "' + query + '"</div>';
                        } else {
                            data.forEach(item => {
                                const el = document.createElement('a');
                                el.href = item.link;
                                el.className = 'search-result-item';

                                let iconClass = 'fa-search';
                                if (item.type === 'student') iconClass = 'fa-user-graduate';
                                if (item.type === 'user' || item.type === 'staff') iconClass = 'fa-user-tie';
                                if (item.type === 'enrollment' || item.type === 'application') iconClass = 'fa-file-signature';
                                if (item.type === 'link') iconClass = 'fa-external-link-alt';

                                el.innerHTML = `
                                    <div class="search-result-icon"><i class="fas ${iconClass}"></i></div>
                                    <div class="search-result-content">
                                        <div class="search-result-title">${item.title}</div>
                                        <div class="search-result-subtitle">${item.subtitle}</div>
                                    </div>
                                `;
                                dropdown.appendChild(el);
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Search Error:', err);
                        dropdown.innerHTML = '<div class="no-results">Error searching. Check console.</div>';
                    });
            }, 300); // 300ms debounce
        });

        // Close on click outside
        document.addEventListener('click', function (e) {
            if (!box.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Re-open on focus if has value
        input.addEventListener('focus', function () {
            if (input.value.length >= 2 && dropdown.children.length > 0) {
                dropdown.classList.add('active');
            }
        });
    });
});
