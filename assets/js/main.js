/**
* Main — JS
    *
 * @package Railpro - Theme
    */

(function ($) {
    'use strict';

    const siteHeader = $('header.elementor-location-header');

    // Header Height
    function setHeaderHeight() {
        if (siteHeader.length) {
            const h = siteHeader.outerHeight();

            document.documentElement.style.setProperty(
                '--header-height',
                h + 'px'
            );
        }
    }

    // Sticky Header
    function handleStickyHeader() {
        if (!siteHeader.length) return;

        if ($(window).scrollTop() > 0 && $(window).width() > 1024) {
            siteHeader.addClass('sticky');
            siteHeader.find('.socials-list').slideUp(150);
        } else {
            siteHeader.removeClass('sticky');
            siteHeader.find('.socials-list').slideDown(150).css('display', 'flex');
        }
    }

    // Viewport Height
    function setViewportHeight() {
        const viewportHeight = window.innerHeight;

        document.documentElement.style.setProperty(
            '--viewport-height',
            viewportHeight + 'px'
        );
    }

    // Tab Auto Play
    function initTabAutoPlay() {
        $('.custom-tabs-autoplay .e-n-tabs').each(function () {
            const $parentWithAttr = $(this).closest('[tabs-duration]');
            const duration = $parentWithAttr.length ? $parentWithAttr.attr('tabs-duration') : '5s';
            this.style.setProperty('--tabs-duration', duration);

            const $titles = $(this).find('.e-n-tab-title');

            if ($titles.length < 2) return;

            let currentIndex = 0;
            let isProgrammatic = false;

            function startAnimation(index) {
                $titles.removeClass('tab-autoplay-active');

                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        $titles.eq(index).addClass('tab-autoplay-active');
                    });
                });
            }

            function goToTab(nextIndex) {
                currentIndex = nextIndex;
                isProgrammatic = true;

                const prevFocus = document.activeElement;

                const evt = new MouseEvent('click', {
                    bubbles: false
                });

                $titles.eq(nextIndex)[0].dispatchEvent(evt);

                isProgrammatic = false;

                startAnimation(nextIndex);

                if (
                    prevFocus &&
                    typeof prevFocus.focus === 'function'
                ) {
                    prevFocus.focus();
                }
            }

            $titles.on('animationend', function () {
                if ($('.elementor-popup-modal:visible').length) {
                    startAnimation(currentIndex);
                    return;
                }

                goToTab((currentIndex + 1) % $titles.length);
            });

            $titles.on('click', function () {
                if (isProgrammatic) return;

                currentIndex = $titles.index(this);

                startAnimation(currentIndex);
            });

            goToTab(0);
        });
    }

    // Portfolio Filter
    function initPortfolioFilter($scope) {
        const $widget = $scope ? $scope.find('.portfolio-filter-widget') : $('.portfolio-filter-widget');
        if (!$widget.length) return;

        const SPINNER_HTML = `
        <div class="pf-spinner-wrap">
            <div class="pf-spinner"></div>
        </div>
    `;
        const STORAGE_KEY_ACTIVE = 'pf_active_category';
        const defaultCat = String($widget.data('default-cat') || 'all');
        let activeCat = sessionStorage.getItem(STORAGE_KEY_ACTIVE) || defaultCat;
        let activeProductPerCat = { [activeCat]: [] };

        const perPage = parseInt($widget.data('per-page')) || 3;
        const templateId = parseInt($widget.data('template-id')) || 0;
        const orderby = String($widget.data('orderby') || 'date');
        const order = String($widget.data('order') || 'DESC');
        const $grid = $widget.find('.pf-grid');


        let currentXhr = null;
        let allProducts = {};
        try {
            allProducts = JSON.parse($widget.find('.pf-products').attr('data-all-products') || '{}');
        } catch (_) { }

        let preloadedData = {};
        try {
            preloadedData = JSON.parse($widget.find('.pf-preloaded-data').html() || '{}');
        } catch (_) { }

        let pinnedMap = {};
        try {
            pinnedMap = JSON.parse($widget.attr('data-pinned-map') || '{}') || {};
        } catch (_) { }

        function getPinnedIds() {
            return (pinnedMap[activeCat] || []).map(String);
        }

        const maxPagesMap = $widget.data('max-pages') || {};
        const categoryCache = {};
        Object.keys(preloadedData).forEach(catId => {
            categoryCache[catId] = {
                html: preloadedData[catId].html || '',
                products: preloadedData[catId].products || {},
                page: 2,
                maxPages: parseInt(maxPagesMap[catId]) || 1,
            };
        });

        if (activeCat !== defaultCat && categoryCache[activeCat]) {
            $grid.html(categoryCache[activeCat].html);
        }

        function updateActiveTabUI() {
            $widget.find('.pf-tab').removeClass('active').attr('aria-selected', 'false');
            $widget.find(`.pf-tab[data-cat="${activeCat}"]`).addClass('active').attr('aria-selected', 'true');
        }

        const $sentinel = $('<div class="pf-sentinel"></div>').insertAfter($grid);
        let scrollObserver = null;

        function createScrollObserver() {
            if (scrollObserver) scrollObserver.disconnect();
            scrollObserver = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !currentXhr) {
                    const cache = categoryCache[activeCat];
                    if (cache && cache.page <= cache.maxPages) loadPosts();
                }
            }, { rootMargin: '0px', threshold: 0 });
            scrollObserver.observe($sentinel[0]);
        }

        function showGridSpinner() { $grid.html(SPINNER_HTML); }
        function showAppendSpinner() { $grid.append($(SPINNER_HTML).addClass('pf-append-spinner')); }
        function removeSpinner() { $grid.find('.pf-spinner-wrap').remove(); }

        // Abort any in-flight request (loadPosts or resetAndLoad)
        function abortCurrent() {
            if (currentXhr) {
                currentXhr.abort();
                currentXhr = null;
            }
        }

        function reconnectObserver() {
            if (scrollObserver) scrollObserver.disconnect();
            requestAnimationFrame(() => {
                if (scrollObserver) scrollObserver.observe($sentinel[0]);
            });
        }

        const getActiveProducts = () => activeProductPerCat[activeCat] || [];
        const setActiveProducts = (arr) => { activeProductPerCat[activeCat] = arr; };

        function toggleActiveProduct(pid) {
            let current = [...getActiveProducts()];
            current = current.includes(pid) ? current.filter(id => id !== pid) : [...current, pid];
            setActiveProducts(current);
        }

        function animateCards($cards) {
            $cards.removeClass('pf-animate-in');
            requestAnimationFrame(() => requestAnimationFrame(() => $cards.addClass('pf-animate-in')));
        }

        // Always save grid HTML when leaving a tab
        function saveCurrentCategoryCache() {
            if (categoryCache[activeCat]) {
                categoryCache[activeCat].html = $grid.html();
            }
        }

        function restoreCategoryCache() {
            const cache = categoryCache[activeCat];
            if (!cache) return;
            $grid.html(cache.html);
            animateCards($grid.find('.pf-card'));
        }

        function rebuildChips() {
            const $container = $widget.find('.pf-products');
            const visibleProducts = categoryCache[activeCat]?.products || {};

            // Remove stale active products that don't exist in this category
            let activeProducts = getActiveProducts().filter(pid => visibleProducts[pid]);
            setActiveProducts(activeProducts);

            const html = Object.entries(visibleProducts).map(([pid, title]) => {
                const isActive = activeProducts.includes(pid);
                return `
                <button class="pf-product-btn ${isActive ? 'active' : ''}" data-product="${pid}">
                    <span class="pf-product-label">${$('<span>').text(title).html()}</span>
                    <span class="pf-close" aria-hidden="true" ${isActive ? '' : 'style="display:none;"'}>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="M6 6 18 18"/>
                        </svg>
                    </span>
                </button>
            `;
            }).join('');

            $container.html(html).toggle(html !== '');
        }

        // Append next page via infinite scroll
        function loadPosts() {
            const cache = categoryCache[activeCat];
            if (currentXhr || cache.page > cache.maxPages) return;

            if (scrollObserver) scrollObserver.unobserve($sentinel[0]);
            showAppendSpinner();

            currentXhr = $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'rp_load_portfolios',
                    paged: cache.page,
                    per_page: perPage,
                    category: activeCat,
                    products: getActiveProducts(),
                    pinned: getPinnedIds(),
                    template_id: templateId,
                    orderby,
                    order,
                },
                success(response) {
                    if (!response.success) return;
                    const $cards = $(response.data.html);
                    removeSpinner();
                    $grid.append($cards);
                    animateCards($cards);
                    cache.page++;
                    cache.maxPages = response.data.max_pages;
                    cache.html = $grid.html();
                },
                complete(_, status) {
                    if (status === 'abort') return;
                    currentXhr = null;
                    if (scrollObserver) scrollObserver.observe($sentinel[0]);
                },
            });
        }

        // Reset to page 1 and reload — aborts any in-flight request first
        function resetAndLoad() {
            const cache = categoryCache[activeCat];
            if (!cache) return;

            abortCurrent();

            cache.page = 1;
            if (scrollObserver) scrollObserver.unobserve($sentinel[0]);
            showGridSpinner();

            currentXhr = $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'rp_load_portfolios',
                    paged: 1,
                    per_page: perPage,
                    category: activeCat,
                    products: getActiveProducts(),
                    pinned: getPinnedIds(),
                    template_id: templateId,
                    orderby,
                    order,
                },
                success(response) {
                    if (!response.success) return;
                    $grid.html(response.data.html);
                    animateCards($grid.find('.pf-card'));
                    cache.page = 2;
                    cache.maxPages = response.data.max_pages;
                    cache.html = $grid.html();
                },
                complete(_, status) {
                    if (status === 'abort') return;
                    currentXhr = null;
                    removeSpinner();
                    if (scrollObserver) scrollObserver.observe($sentinel[0]);
                },
            });
        }

        $widget.on('click', '.pf-tab', function () {
            const newCat = String($(this).data('cat'));
            if (newCat === activeCat) return;

            abortCurrent();

            if (scrollObserver) scrollObserver.disconnect();

            saveCurrentCategoryCache();
            activeCat = newCat;
            sessionStorage.setItem(STORAGE_KEY_ACTIVE, activeCat);

            if (!(activeCat in activeProductPerCat)) activeProductPerCat[activeCat] = [];

            updateActiveTabUI();
            restoreCategoryCache();
            rebuildChips();
            reconnectObserver();
        });

        $widget.on('click', '.pf-product-btn', function (e) {
            if ($(e.target).closest('.pf-close').length) return;
            const pid = String($(this).data('product'));
            toggleActiveProduct(pid);
            rebuildChips();
            resetAndLoad();
        });

        $widget.on('click', '.pf-close', function (e) {
            e.stopPropagation();
            const pid = String($(this).closest('.pf-product-btn').data('product'));
            setActiveProducts(getActiveProducts().filter(id => id !== pid));
            rebuildChips();
            resetAndLoad();
        });

        updateActiveTabUI();
        animateCards($grid.find('.pf-card'));
        rebuildChips();
        createScrollObserver();

        const urlParams = new URLSearchParams(location.search);
        const urlCatSlug = urlParams.get('portfolio_cat');
        if (urlCatSlug) {
            setTimeout(function () {
                $widget.find('.pf-tab').filter(function () {
                    return $(this).text().toLowerCase().includes(urlCatSlug.toLowerCase());
                }).trigger('click');
            }, 100);
        }
    }

    // Table of Content All
    function initTableOfContentAll($scope) {
        var $widgets = $scope ? $scope.find('.table-of-content-all.all') : $('.table-of-content-all.all');

        $widgets.each(function () {
            var $toc = $(this);

            var isEditor = Boolean(
                typeof elementorFrontend !== 'undefined' &&
                elementorFrontend.isEditMode &&
                elementorFrontend.isEditMode()
            );

            // Skip re-init on frontend; always re-render in editor
            if (!isEditor) {
                if ($toc.data('toc-initialized')) return;
                $toc.data('toc-initialized', true);
            }

            var tocId = $toc.attr('id');

            // Remove previous scroll listener before rebuilding
            $(window).off('scroll.tocall-' + tocId);

            // Read settings from data attributes
            var includeTags = ($toc.attr('data-toc-tags') || 'h2').split(',').map(Function.prototype.call, String.prototype.trim).filter(Boolean);
            var excludeTags = ($toc.attr('data-toc-exclude') || '').split(',').map(Function.prototype.call, String.prototype.trim).filter(Boolean);
            var containerSel = ($toc.attr('data-toc-container') || '').trim();
            var marker = ($toc.attr('data-toc-marker') || 'bullets').trim();
            var iconClass = ($toc.attr('data-toc-icon') || '').trim();
            var noHeadingsMsg = $toc.attr('data-toc-noheadings') || 'No headings were found on this page.';

            var $list = $toc.find('.table-of-content-all__list');

            // Determine search root
            var $root = $('body');
            if (containerSel) {
                var $container = $(containerSel).first();
                if ($container.length) {
                    $root = $container;
                } else {
                    console.warn('[TOC] Container "' + containerSel + '" not found, falling back to body.');
                }
            }

            // Build active tag list (include minus exclude)
            var activeTags = includeTags.filter(function (tag) {
                return excludeTags.indexOf(tag) === -1;
            });

            if (!activeTags.length) {
                $list.html('<li class="table-of-content-all__no-headings">' + $('<span>').text(noHeadingsMsg).html() + '</li>');
                return;
            }

            // Collect headings & auto-assign IDs
            var headings = [];
            var idCounter = {};

            $root.find(activeTags.join(',')).each(function () {
                var $h = $(this);

                // Skip headings inside the TOC widget itself
                if ($h.closest('.table-of-content-all').length) return;

                if ($h.closest('.elementor-hidden-desktop.elementor-hidden-tablet.elementor-hidden-mobile').length) return;
                if (!$h.is(':visible')) return;

                var text = $h.text().trim();
                if (!text) return;

                var id = $h.attr('id');

                if (!id) {
                    // Generate slug from heading text
                    var slug = text
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '')
                        .substring(0, 60) || 'heading';

                    // Deduplicate slugs
                    if (idCounter[slug] === undefined) {
                        idCounter[slug] = 0;
                    } else {
                        slug = slug + '-' + (++idCounter[slug]);
                    }

                    id = slug;
                    $h.attr('id', id);
                }

                headings.push({ id: id, text: text, $el: $h });
            });

            // Remove duplicate IDs
            headings = headings.filter(function (h, idx, arr) {
                return arr.findIndex(function (x) { return x.id === h.id; }) === idx;
            });

            if (!headings.length) {
                $list.html('<li class="table-of-content-all__no-headings">' + $('<span>').text(noHeadingsMsg).html() + '</li>');
                return;
            }

            // Render TOC list
            var html = '';
            headings.forEach(function (h, i) {
                html += '<li class="table-of-content-all__item" data-toc-id="' + h.id + '">';

                if (marker === 'number') {
                    html += '<span class="table-of-content-all__marker">' + (i + 1) + '.</span>';
                } else if (marker === 'bullets' && iconClass) {
                    html += '<span class="table-of-content-all__icon"><i class="' + iconClass + '"></i></span>';
                }

                html += '<a class="table-of-content-all__heading" href="#' + h.id + '">'
                    + $('<span>').text(h.text).html()
                    + '</a></li>';
            });

            $list.html(html);

            // Editor only needs the list rendered, no scroll interactions
            if (isEditor) return;

            var $items = $list.find('.table-of-content-all__item');
            var $links = $list.find('.table-of-content-all__heading');

            // Prevent scroll listener from overriding click-triggered active state
            var isScrollingFromClick = false;
            var scrollEndTimer = null;
            var rafPending = false;

            function getScrollOffset() {
                return parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-height')) || 0;
            }

            // Set active item by heading ID only (no URL change)
            function setActiveById(id) {
                $items.removeClass('active');
                $items.filter('[data-toc-id="' + id + '"]').addClass('active');
            }

            // Scroll: set active only, do NOT touch URL hash
            function applyActiveScroll(id) {
                setActiveById(id);
            }

            // Click: set active + update URL hash via replaceState
            function applyActiveClick(id) {
                setActiveById(id);
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, null, '#' + id);
                }
            }

            // Smooth scroll to a heading + optionally push a history entry
            function scrollToHeading(id, pushState) {
                var target = document.getElementById(id);
                if (!target) return;

                var top = target.getBoundingClientRect().top + window.pageYOffset - getScrollOffset() - 10;

                isScrollingFromClick = true;
                clearTimeout(scrollEndTimer);

                window.scrollTo({ top: top, behavior: 'smooth' });
                setActiveById(id);

                if (pushState && window.history && window.history.pushState) {
                    window.history.pushState(null, null, '#' + id);
                }

                // Re-enable scroll listener once smooth scroll finishes
                if ('onscrollend' in window) {
                    window.addEventListener('scrollend', function onEnd() {
                        isScrollingFromClick = false;
                        window.removeEventListener('scrollend', onEnd);
                    }, { once: true });
                } else {
                    scrollEndTimer = setTimeout(function () {
                        isScrollingFromClick = false;
                    }, 800);
                }
            }

            // Click: scroll to heading + push URL hash
            $links.on('click', function (e) {
                e.preventDefault();
                scrollToHeading($(this).attr('href').replace('#', ''), true);
            });

            // On page load: scroll to hash heading, or auto-active first heading
            var initialHash = window.location.hash.replace('#', '');
            if (initialHash) {
                setTimeout(function () {
                    var match = headings.filter(function (h) { return h.id === initialHash; });
                    if (match.length) scrollToHeading(initialHash, false);
                }, 300);
            } else {
                // No hash → auto-active the first heading on load/reload
                setTimeout(function () {
                    setActiveById(headings[0].id);
                }, 100);
            }

            // Browser back/forward button
            $(window).off('popstate.tocall-' + tocId).on('popstate.tocall-' + tocId, function () {
                var hash = window.location.hash.replace('#', '');
                if (hash) {
                    var match = headings.filter(function (h) { return h.id === hash; });
                    if (match.length) scrollToHeading(hash, false);
                } else {
                    // Back to no-hash state → active first heading
                    setActiveById(headings[0].id);
                }
            });

            // Scroll listener: update active based on scroll position, never touches URL
            function updateActiveOnScroll() {
                if (isScrollingFromClick) return;

                var scrollOffset = getScrollOffset();
                var scrollPos = window.pageYOffset + scrollOffset + 20;

                // Scrolled past the last heading → keep last heading active
                var lastEl = headings[headings.length - 1].$el[0];
                var lastBottom = lastEl.getBoundingClientRect().bottom + window.pageYOffset;
                if (window.pageYOffset > lastBottom) {
                    return;
                }

                // Find the deepest heading that has been scrolled past
                var found = false;
                for (var i = headings.length - 1; i >= 0; i--) {
                    var top = headings[i].$el[0].getBoundingClientRect().top + window.pageYOffset;
                    if (top <= scrollPos) {
                        var currentId = $items.filter('.active').attr('data-toc-id');
                        if (currentId !== headings[i].id) applyActiveScroll(headings[i].id);
                        found = true;
                        break;
                    }
                }

                // Scrolled above all headings (top of page) → active first heading
                if (!found) {
                    var currentId = $items.filter('.active').attr('data-toc-id');
                    if (currentId !== headings[0].id) applyActiveScroll(headings[0].id);
                }
            }

            // Throttle scroll updates with requestAnimationFrame
            function onScroll() {
                if (rafPending) return;
                rafPending = true;
                requestAnimationFrame(function () {
                    updateActiveOnScroll();
                    rafPending = false;
                });
            }

            $(window).on('scroll.tocall-' + tocId, onScroll);
            updateActiveOnScroll();
        });
    }

    // Elementor Editor hooks
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/table-of-content-all.default',
            initTableOfContentAll
        );

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/portfolio-filter.default',
            function ($scope) {
                initPortfolioFilter($scope);
            }
        );
    });

    // Menu Current Page
    function initMenuCurrentPage() {
        var currentPath = window.location.pathname
            .replace(/\/$/, '')
            .toLowerCase();

        $('nav.e-n-menu').each(function () {
            $(this).find('.e-n-menu-item').each(function () {
                $(this).find('.elementor-icon-list-items li').each(function () {
                    var $li = $(this);
                    var $a = $li.find('a').first();

                    if (!$a.length) return;

                    var href = $a.attr('href') || '';
                    var linkPath;

                    try {
                        linkPath = new URL(href, window.location.origin).pathname
                            .replace(/\/$/, '')
                            .toLowerCase();
                    } catch (e) {
                        linkPath = href
                            .replace(/\/$/, '')
                            .toLowerCase();
                    }


                    if (linkPath && linkPath === currentPath) {
                        $li.find('.elementor-icon-list-text').addClass('current-page');
                    }
                });
            });
        });
    }
    // Hero Parallax 
    let heroParallaxData = [];
    let heroParallaxTicking = false;

    function calculateHeroParallaxData() {
        const heroes = document.querySelectorAll('.hero-parallax');
        heroParallaxData = Array.from(heroes).map(hero => {
            const rect = hero.getBoundingClientRect();
            return {
                el: hero,
                height: rect.height
            };
        });
    }

    function updateHeroParallax() {
        const scrollY = window.pageYOffset;
        heroParallaxData.forEach(data => {
            const height = data.height;
            let progress = scrollY / height;
            progress = Math.max(0, Math.min(progress, 1));
            const scale = 1 + progress * 0.1;
            const y = progress * 100;
            data.el.style.setProperty('--parallax-scale', scale.toFixed(4));
            data.el.style.setProperty('--parallax-y', y.toFixed(2) + 'px');
        });
    }

    function onHeroParallaxScroll() {
        if (!heroParallaxTicking) {
            window.requestAnimationFrame(function () {
                updateHeroParallax();
                heroParallaxTicking = false;
            });
            heroParallaxTicking = true;
        }
    }

    // Gallery Single Product
    function initProductGallery($scope) {
        const $targets = $scope
            ? $scope.find('.rp-product-gallery-wrapper')
            : $('.rp-product-gallery-wrapper');

        $targets.each(function () {
            const $wrapper = $(this);
            const $grid = $wrapper.find('.rp-gallery-grid');
            const $btn = $wrapper.find('.rp-load-more-btn');
            const $loader = $wrapper.find('.rp-loader');

            if (!$grid.length) return;

            const iso = new Isotope($grid[0], {
                itemSelector: '.rp-gallery-item',
                percentPosition: true,

                masonry: {
                    columnWidth: '.rp-gallery-sizer',
                    horizontalOrder: true
                }
            });

            $grid.data('isotope', iso);
            $grid.addClass('is-initialized');

            $grid.imagesLoaded()
                .progress(function () {
                    iso.layout();
                })

                .always(function () {
                    setTimeout(function () {
                        iso.layout();
                    }, 500);
                });

            const originalText = $btn.text().trim();

            $btn.on('click', function () {
                const perPage = $wrapper.data('per-page');

                // Show Less
                if ($btn.hasClass('is-show-less')) {
                    const $allItems = $grid.find('.rp-gallery-item');

                    if ($allItems.length > perPage) {
                        const $itemsToRemove = $allItems.slice(perPage);

                        iso.remove($itemsToRemove);
                        iso.layout();

                        $btn
                            .removeClass('is-show-less')
                            .text(originalText);

                        $('html, body').animate(
                            {
                                scrollTop:
                                    $wrapper.offset().top - 120
                            },
                            500
                        );
                    }

                    return;
                }

                // Load More
                const productId = $wrapper.data('product-id');
                const source = $wrapper.data('source') || 'product';
                const currentCount = $grid.find(
                    '.rp-gallery-item'
                ).length;

                $btn.addClass('loading').hide();
                $loader.show();

                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'POST',

                    data: {
                        action: 'rp_load_more_gallery',
                        nonce: ajax_object.nonce,
                        product_id: productId,
                        offset: currentCount,
                        per_page: perPage,
                        source: source
                    },

                    success: function (response) {
                        if (response.success) {
                            const $newItems = $(
                                response.data.html
                            );

                            $grid.append($newItems);

                            $newItems.each(function () {
                                const idx = $(this).data('index');

                                $wrapper
                                    .find(
                                        '.rp-hidden-gallery-item[data-index="' +
                                        idx +
                                        '"]'
                                    )
                                    .remove();
                            });

                            $newItems.imagesLoaded(function () {
                                iso.appended($newItems);
                                iso.layout();
                                if (typeof Fancybox !== 'undefined') {
                                    Fancybox.destroy();
                                    fancyboxInitialized = false;
                                    initFancybox();
                                }
                            });

                            $btn
                                .removeClass('loading')
                                .show();

                            if (!response.data.remaining) {
                                $btn
                                    .addClass('is-show-less')
                                    .text('Show Less');
                            }
                        } else {
                            $btn
                                .removeClass('loading')
                                .show();

                            console.error(
                                'Gallery load more failed:',
                                response
                            );
                        }
                    },

                    error: function (xhr, status, error) {
                        $btn
                            .removeClass('loading')
                            .show();

                        console.error(
                            'Gallery AJAX error:',
                            error
                        );
                    },

                    complete: function () {
                        $loader.hide();
                    }
                });
            });
        });
    }

    // Fancybox
    let fancyboxInitialized = false;

    function initFancybox() {
        if (typeof Fancybox === 'undefined') return;
        if (fancyboxInitialized) return;

        Fancybox.bind('[data-fancybox="gallery"]', {
            infinite: true,
            Hash: false,
            wheel: "zoom",
            dragToClose: false,

            Image: {
                Panzoom: {
                    lockAxis: false
                }
            },

            Toolbar: {
                display: [
                    { id: 'counter', position: 'center' },
                    'zoom',
                    'slideshow',
                    'fullscreen',
                    'close'
                ]
            },

            on: {
                reveal: (fancybox, slide) => {
                    const idx = slide.index + 1;
                    const $content = $(slide.$el).find('.fancybox__content');
                    if ($content.find('.fancy-index').length === 0) {
                        $content.append('<span class="fancy-index">' + idx + '</span>');
                    }

                    const $img = $content.find('.fancybox__image')[0];
                    const $index = $content.find('.fancy-index')[0];

                    if ($img && $index && !$index.dataset.synced) {
                        $index.dataset.synced = 'true';

                        const syncTransform = () => {
                            if (!$index.isConnected) {
                                return;
                            }
                            const imgRect = $img.getBoundingClientRect();
                            const contentRect = $content[0].getBoundingClientRect();
                            const diffLeft = imgRect.left - contentRect.left;
                            const diffBottom = contentRect.bottom - imgRect.bottom;
                            $index.style.transform = `translate(${diffLeft}px, ${-diffBottom}px)`;
                            requestAnimationFrame(syncTransform);
                        };

                        requestAnimationFrame(syncTransform);
                    }
                }
            },

            caption: function (fancybox, carousel, slide) {
                const text = (slide.triggerEl ? $(slide.triggerEl).attr('data-caption') : null)
                    || slide.caption
                    || '';

                return text;
            }
        });

        fancyboxInitialized = true;
    }

    // Custom Slider Control
    function rpCustomSliderControl() {
        $(document).on(
            'click',
            '[class*="bt-click-right-"], [class*="bt-click-left-"]',
            function (e) {
                e.preventDefault();

                console.log('click');

                var $button = $(this);
                var buttonClasses = $button
                    .attr('class')
                    .split(/\s+/);

                var sliderName = '';
                var direction = '';

                buttonClasses.forEach(function (className) {
                    if (
                        className.indexOf(
                            'bt-click-right-'
                        ) === 0
                    ) {
                        sliderName = className.replace(
                            'bt-click-right-',
                            ''
                        );

                        direction = 'next';
                    } else if (
                        className.indexOf(
                            'bt-click-left-'
                        ) === 0
                    ) {
                        sliderName = className.replace(
                            'bt-click-left-',
                            ''
                        );

                        direction = 'prev';
                    }
                });

                if (sliderName && direction) {
                    var $slider = $('.' + sliderName);

                    if ($slider.length > 0) {
                        var $slickInside = $slider.find(
                            '.slick-initialized'
                        );

                        var $targetSlider =
                            $slider.hasClass(
                                'slick-initialized'
                            )
                                ? $slider
                                : $slickInside.length > 0
                                    ? $slickInside
                                    : null;

                        if ($targetSlider) {
                            $targetSlider.slick(
                                direction === 'next'
                                    ? 'slickNext'
                                    : 'slickPrev'
                            );
                        } else {
                            var arrowSelector =
                                direction === 'next'
                                    ? '.slick-next, .swiper-button-next, .elementor-swiper-button-next'
                                    : '.slick-prev, .swiper-button-prev, .elementor-swiper-button-prev';

                            $slider
                                .find(arrowSelector)
                                .first()
                                .trigger('click');
                        }
                    }
                }
            }
        );
    }
    // Value Scroll — jQuery fallback for Safari / Firefox
    function initValueScroll($scope) {
        var $targets = $scope
            ? $scope.find('.rp-value-scroll[data-animate="true"]')
            : $('.rp-value-scroll[data-animate="true"]');

        $targets.each(function () {
            var $section = $(this);
            var $items = $section.find('li');

            if (!$items.length) return;

            $items.first().addClass('is-active');

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        $items.removeClass('is-active');
                        $(entry.target).addClass('is-active');
                    }
                });
            }, {
                rootMargin: '-45% 0px -45% 0px',
                threshold: 0.5
            });

            $items.each(function () {
                observer.observe(this);
            });
        });
    }

    // Gravity Forms Loading State
    function initGformLoading() {
        $(document).on('submit', '.gform_wrapper form', function () {
            $(this).addClass('loading');
            $(this).find('.gform_footer').addClass('loading');
            $(this).find('.gform_button, input[type="submit"]').addClass('loading');
        });
    }

    // Layout 2 columns for Gravity Forms using flex wrapper
    function layoutRpTwoColumnForm() {
        $('.gform_wrapper.gform_wrapper form.rp-two-column').each(function () {
            var $form = $(this);
            var $fieldsContainer = $form.find('.gform_fields');

            // Avoid double wrapping
            if ($fieldsContainer.find('.rp-col-left').length) return;

            var $breakField = $fieldsContainer.find('.rp-break-column');
            if (!$breakField.length) return;

            var $colLeft = $('<div class="rp-col-left"></div>');
            var $colRight = $('<div class="rp-col-right"></div>');

            var $allFields = $fieldsContainer.children();
            var breakIndex = $allFields.index($breakField);

            if (breakIndex !== -1) {
                var $leftElements = $allFields.slice(0, breakIndex);
                var $rightElements = $allFields.slice(breakIndex);

                $colLeft.append($leftElements);
                $colRight.append($rightElements);

                $fieldsContainer.append($colLeft).append($colRight);
            }
        });

        applyRpTwoColumnStyles();
    }
    function applyRpTwoColumnStyles() {
        $('.gform_wrapper.gform_wrapper form.rp-two-column').each(function () {
            var $form = $(this);
            var $fieldsContainer = $form.find('.gform_fields');
            var $colLeft = $fieldsContainer.find('.rp-col-left');
            var $colRight = $fieldsContainer.find('.rp-col-right');

            if (!$colLeft.length || !$colRight.length) return;

            if ($(window).width() >= 767) {
                // Desktop styling (side-by-side equal height)
                $fieldsContainer.css({
                    display: 'flex',
                    gap: '32px',
                    'column-count': 'auto'
                });
                $colLeft.css({
                    flex: '1',
                    display: 'flex',
                    'flex-direction': 'column'
                });
                $colRight.css({
                    flex: '1',
                    display: 'flex',
                    'flex-direction': 'column'
                });
                $fieldsContainer.find('.rp-break-column').css({
                    'break-before': 'auto',
                    '-webkit-column-break-before': 'auto',
                    'margin-top': '0'
                });
            } else {
                // Mobile styling (stacked vertical)
                $fieldsContainer.css({
                    display: 'block',
                    'column-count': '1'
                });
                $colLeft.css({
                    display: 'block'
                });
                $colRight.css({
                    display: 'block'
                });
                $fieldsContainer.find('.rp-break-column').css({
                    'margin-top': '30px'
                });
            }
        });
    }

    $(document).on('click', '.elementor-nav-menu .menu-item-has-children > a', function (e) {
        // If the user clicks on the chevron (sub-arrow), prevent the default link navigation
        if ($(e.target).closest('.sub-arrow').length) {
            e.preventDefault();
        }
    });
    $(document).on('click', '.gform_wrapper .gfield--type-fileupload .gform_drop_area', function (e) {
        if ($(e.target).is('button')) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        const gField = $(this).closest('.gfield');

        gField.find('button.gform_button_select_files').trigger('click');
    });
    //scroll gallery button click handler
    $(document).on('click', '.scroll-to-gallery, a[href="#gallery"]', function (e) {
        e.preventDefault();
        const target = $('#gallery, .rp-product-gallery-wrapper').first();
        if (target.length) {
            let offset = target.offset().top;
            if (siteHeader.length) {
                offset -= siteHeader.outerHeight();
            }
            offset -= -20;
            window.scrollTo({
                top: offset,
                behavior: 'smooth'
            });
        }
    });

    function initBackToTop() {
        const $btn = $('#rp-back-to-top');
        if (!$btn.length) return;

        $(window).on('scroll', function () {
            if ($(window).scrollTop() > 400) {
                $btn.addClass('is-visible');
            } else {
                $btn.removeClass('is-visible');
            }
        });

        $btn.on('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Init
    $(function () {
        setHeaderHeight();
        setViewportHeight();
        handleStickyHeader();

        $(window).on('resize', function () {
            handleStickyHeader();
            setHeaderHeight();
            setViewportHeight();
            calculateHeroParallaxData();
            onHeroParallaxScroll();
            applyRpTwoColumnStyles();
        });

        $(window).on('scroll', function () {
            handleStickyHeader();
            if ($('body').hasClass('menu-open')) {
                closeMenu();
            }
            onHeroParallaxScroll();
        });

        $(window).on('load', function () {
            handleStickyHeader();
            setHeaderHeight();
        });

        setTimeout(initTabAutoPlay, 300);
        calculateHeroParallaxData();
        updateHeroParallax();

        initProductGallery();
        initFancybox();

        rpCustomSliderControl();
        initValueScroll();
        initGformLoading();
        layoutRpTwoColumnForm();
        initTableOfContentAll(null);
        initMenuCurrentPage();
        initBackToTop();
    });
})(jQuery);