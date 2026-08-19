(function () {
    'use strict';

    var config = window.catalogConfig || {};

    var catalogEl = document.querySelector('.js-catalog');
    if (!catalogEl) {
        return;
    }

    var gridEl = catalogEl.querySelector('.js-catalog-grid');
    var cardsEl = gridEl ? gridEl.querySelector('.cards') : null;
    var sentinelEl = catalogEl.querySelector('.js-catalog-sentinel');
    var loaderEl = catalogEl.querySelector('.js-catalog-loader');
    var countEl = catalogEl.querySelector('.js-catalog-count');
    var searchForm = catalogEl.querySelector('.js-catalog-search-form');
    var searchInput = catalogEl.querySelector('.js-catalog-search-input');
    var menuEl = catalogEl.querySelector('.js-catalog-menu');
    var menuBodyEl = catalogEl.querySelector('.js-catalog-menu-body');
    var categoryFilter = catalogEl.querySelector('.js-catalog-category-filter');
    var categoryFilterInput = catalogEl.querySelector('.js-catalog-category-filter-input');
    var categoryReset = catalogEl.querySelector('.js-catalog-category-reset');
    var categoryEmpty = catalogEl.querySelector('.js-catalog-category-empty');
    var titleEl = catalogEl.querySelector('.js-catalog-title');

    var loadUrl = config.loadUrl || '/catalog/load';
    var cursor = config.next_cursor || null;
    var hasMore = !!config.has_more;
    var search = config.search || '';
    var category = config.category || '';
    var loading = false;
    var observer = null;
    var suppressLoad = false;

    function updateCount(total) {
        if (!countEl) {
            return;
        }

        var label = countEl.dataset.label || '';
        countEl.textContent = total + (label ? ' ' + label : '');
    }

    function setLoading(state) {
        loading = state;
        if (loaderEl) {
            loaderEl.hidden = !state;
        }
    }

    function buildUrl(nextCursor) {
        var params = new URLSearchParams();

        if (nextCursor !== null && nextCursor !== undefined) {
            params.set('cursor', nextCursor);
        }

        if (search) {
            params.set('search', search);
        }

        if (category) {
            params.set('category', category);
        }

        return loadUrl + '?' + params.toString();
    }

    function appendCards(html) {
        if (!cardsEl) {
            return;
        }

        var tpl = document.createElement('template');
        tpl.innerHTML = html.trim();
        var nodes = tpl.content.childNodes;

        nodes.forEach(function (node) {
            if (node.nodeType === 1) {
                cardsEl.appendChild(node);
            }
        });
    }

    function replaceCards(html) {
        if (!cardsEl) {
            return;
        }

        cardsEl.innerHTML = '';
        appendCards(html);
    }

    function scrollToProducts() {
        if (!gridEl) {
            return;
        }

        try {
            gridEl.scrollIntoView({ block: 'start', behavior: 'smooth' });
        } catch (e) {
            gridEl.scrollIntoView();
        }
    }

    function loadNext() {
        if (loading || !hasMore) {
            return;
        }

        setLoading(true);
        var nextCursor = cursor;

        fetch(buildUrl(nextCursor), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (data.status === 'redirect' && data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            if (data.status === 'success') {
                if (data.html) {
                    appendCards(data.html);
                }
                cursor = data.next_cursor || null;
                hasMore = !!data.has_more;
                updateCount(data.total || 0);
            } else {
                hasMore = false;
            }
        })
        .catch(function () {
            hasMore = false;
        })
        .then(function () {
            setLoading(false);
        });
    }

    function resetAndLoad() {
        cursor = null;
        suppressLoad = true;

        if (gridEl) {
            gridEl.classList.add('is-loading');
        }

        setLoading(true);

        fetch(buildUrl(null), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (data.status === 'redirect' && data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            if (data.status === 'success') {
                if (data.html) {
                    replaceCards(data.html);
                }
                cursor = data.next_cursor || null;
                hasMore = !!data.has_more;
                updateCount(data.total || 0);
                scrollToProducts();
            } else {
                hasMore = false;
            }
        })
        .catch(function () {
            hasMore = false;
        })
        .then(function () {
            setLoading(false);
            if (gridEl) {
                gridEl.classList.remove('is-loading');
            }
            window.setTimeout(function () {
                suppressLoad = false;
            }, 600);
        });
    }

    function activateCategory(button) {
        if (!menuEl) {
            return;
        }

        var items = menuEl.querySelectorAll('.js-catalog-category');
        items.forEach(function (item) {
            item.classList.remove('is-active');
        });

        if (button) {
            button.classList.add('is-active');
        }
    }

    function updateTitle() {
        if (!titleEl) {
            return;
        }

        var items = menuEl ? menuEl.querySelectorAll('.js-catalog-category') : [];
        var label = '';

        items.forEach(function (item) {
            if (item.classList.contains('is-active')) {
                label = item.textContent.trim();
            }
        });

        if (label) {
            titleEl.textContent = label;
        }
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            search = searchInput ? searchInput.value.trim() : '';
            category = '';
            activateCategory(null);
            updateTitle();
            resetAndLoad();
        });
    }

    if (menuEl) {
        menuEl.addEventListener('click', function (e) {
            var button = e.target.closest('.js-catalog-category');
            if (!button) {
                return;
            }

            category = button.getAttribute('data-category') || '';
            search = '';

            if (searchInput) {
                searchInput.value = '';
            }

            activateCategory(button);
            updateTitle();
            resetAndLoad();
        });
    }

    if (categoryFilterInput && menuEl) {
        function applyCategoryFilter() {
            var query = categoryFilterInput.value.trim().toLowerCase();
            var items = menuBodyEl ? menuBodyEl.querySelectorAll('.js-catalog-category') : [];
            var visibleCount = 0;

            items.forEach(function (item) {
                if (item.dataset.category === '') {
                    item.style.display = '';
                    return;
                }

                var match = item.textContent.trim().toLowerCase().indexOf(query) !== -1;
                item.style.display = match ? '' : 'none';

                if (match) {
                    visibleCount++;
                }
            });

            if (categoryFilter) {
                categoryFilter.classList.toggle('is-filtering', query !== '');
            }

            if (categoryReset) {
                categoryReset.hidden = query === '';
            }

            if (categoryEmpty) {
                categoryEmpty.hidden = visibleCount > 0 || query === '';
            }
        }

        categoryFilterInput.addEventListener('input', applyCategoryFilter);

        if (categoryReset) {
            categoryReset.addEventListener('click', function () {
                categoryFilterInput.value = '';
                applyCategoryFilter();
                categoryFilterInput.focus();
            });
        }
    }

    if (sentinelEl && 'IntersectionObserver' in window) {
        observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (suppressLoad) {
                    return;
                }

                if (entry.isIntersecting) {
                    loadNext();
                }
            });
        }, { rootMargin: '200px 0px' });

        observer.observe(sentinelEl);
    } else if (sentinelEl) {
        window.addEventListener('scroll', function () {
            if (suppressLoad) {
                return;
            }
            var rect = sentinelEl.getBoundingClientRect();
            if (rect.top < window.innerHeight + 200) {
                loadNext();
            }
        }, { passive: true });
    }
})();
