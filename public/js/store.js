(function() {
    'use strict';

    // Hero slider
    var slides = document.querySelectorAll('.hero-slide');
    if (slides.length) {
        var dotsContainer = document.getElementById('hero-dots');
        if (dotsContainer) {
            slides.forEach(function(_, i) {
                var dot = document.createElement('span');
                dot.setAttribute('data-index', i);
                dotsContainer.appendChild(dot);
            });
        }
        var dots = dotsContainer ? dotsContainer.querySelectorAll('span') : [];
        var current = 0;
        function goTo(n) {
            current = (n + slides.length) % slides.length;
            slides.forEach(function(s, i) { s.classList.toggle('active', i === current); });
            dots.forEach(function(d, i) { d.classList.toggle('active', i === current); });
        }
        dots.forEach(function(d, i) { d.addEventListener('click', function() { goTo(i); }); });
        setInterval(function() { goTo(current + 1); }, 5000);
    }

    // Mobile menu toggle
    var menuToggle = document.getElementById('menu-toggle');
    var navCenter = document.querySelector('.store-nav .nav-center');
    if (menuToggle && navCenter) {
        menuToggle.addEventListener('click', function() {
            navCenter.style.display = navCenter.style.display === 'flex' ? 'none' : 'flex';
            if (navCenter.style.position !== 'absolute') {
                navCenter.style.position = 'absolute';
                navCenter.style.top = '72px';
                navCenter.style.left = '0';
                navCenter.style.right = '0';
                navCenter.style.background = '#fff';
                navCenter.style.flexDirection = 'column';
                navCenter.style.padding = '20px';
                navCenter.style.borderBottom = '1px solid #EAEAEA';
            }
        });
    }

    // Search autocomplete (demo: show suggestions on focus)
    var searchInput = document.getElementById('search-input');
    var searchDropdown = document.getElementById('search-dropdown');
    if (searchInput && searchDropdown) {
        var suggestions = ['iPhone 15 Pro', 'ThinkPad X1', 'Honor Magic 6', 'MacBook Pro', 'Lenovo IdeaPad'];
        searchInput.addEventListener('focus', function() {
            searchDropdown.innerHTML = suggestions.map(function(s) {
                return '<a href="/store?q=' + encodeURIComponent(s) + '" style="display:block;padding:12px 20px;color:#222;">' + s + '</a>';
            }).join('');
            searchDropdown.parentElement.classList.add('open');
        });
        searchInput.addEventListener('blur', function() {
            setTimeout(function() { searchDropdown.parentElement.classList.remove('open'); }, 200);
        });
    }

    // AJAX add to cart + fly animation + badge
    function getCsrfToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function syncProductQtyToForm(form) {
        var pageQty = document.getElementById('qty');
        if (!pageQty) return;
        var v = Math.max(1, Math.min(parseInt(pageQty.max, 10) || 99, parseInt(pageQty.value, 10) || 1));
        var h = form.querySelector('#qty-hidden');
        var s = form.querySelector('#qty-sticky');
        if (h) h.value = v;
        if (s) s.value = v;
    }

    function findProductImageSrc(form) {
        var card = form.closest('.product-card');
        if (card) {
            var img = card.querySelector('.product-image img');
            if (img && img.src) return img.src;
        }
        var main = document.querySelector('.product-gallery .main-image img');
        if (main && main.src) return main.src;
        return '';
    }

    function findImageElementForFly(form) {
        var card = form.closest('.product-card');
        if (card) {
            var img = card.querySelector('.product-image img');
            if (img) return img;
        }
        return document.querySelector('.product-gallery .main-image img');
    }

    function flyToCart(imgSrc, startEl) {
        var cart = document.getElementById('cart-icon-link');
        if (!cart || !imgSrc) return Promise.resolve();

        return new Promise(function(resolve) {
            var startRect = startEl ? startEl.getBoundingClientRect() : { left: window.innerWidth / 2, top: window.innerHeight / 2, width: 60, height: 60 };
            var cartRect = cart.getBoundingClientRect();
            var w = Math.min(startRect.width, 72);
            var h = Math.min(startRect.height, 72);
            var fly = document.createElement('img');
            fly.src = imgSrc;
            fly.alt = '';
            fly.setAttribute('aria-hidden', 'true');
            fly.style.cssText = 'position:fixed;z-index:10000;left:' + (startRect.left + (startRect.width - w) / 2) + 'px;top:' + (startRect.top + (startRect.height - h) / 2) + 'px;width:' + w + 'px;height:' + h + 'px;object-fit:cover;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,.18);pointer-events:none;';
            document.body.appendChild(fly);

            var dx = (cartRect.left + cartRect.width / 2) - (startRect.left + startRect.width / 2);
            var dy = (cartRect.top + cartRect.height / 2) - (startRect.top + startRect.height / 2);

            var anim = fly.animate(
                [
                    { transform: 'translate(0,0) scale(1)', opacity: 1 },
                    { transform: 'translate(' + dx + 'px,' + dy + 'px) scale(0.12)', opacity: 0.85 }
                ],
                { duration: 680, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' }
            );
            anim.onfinish = function() {
                fly.remove();
                resolve();
            };
        });
    }

    function updateCartBadge(count) {
        var badge = document.getElementById('cart-badge');
        if (!badge) return;
        if (count < 1) {
            badge.style.display = 'none';
            badge.textContent = '0';
            return;
        }
        badge.style.display = 'flex';
        badge.textContent = count > 99 ? '99+' : String(count);
        badge.classList.remove('cart-badge--pulse');
        void badge.offsetWidth;
        badge.classList.add('cart-badge--pulse');
    }

    function showToast(msg) {
        var t = document.createElement('div');
        t.className = 'cart-toast';
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:32px;left:50%;transform:translateX(-50%);background:#111;color:#fff;padding:12px 24px;border-radius:8px;font-size:14px;z-index:10001;box-shadow:0 8px 24px rgba(0,0,0,.2);';
        document.body.appendChild(t);
        setTimeout(function() {
            t.style.opacity = '0';
            t.style.transition = 'opacity .3s';
            setTimeout(function() { t.remove(); }, 300);
        }, 2200);
    }

    document.querySelectorAll('form.js-add-to-cart-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!window.fetch || !getCsrfToken()) {
                return;
            }
            e.preventDefault();
            syncProductQtyToForm(form);

            var btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            var imgSrc = findProductImageSrc(form);
            var startEl = findImageElementForFly(form);

            var runFly = function() {
                return flyToCart(imgSrc, startEl);
            };

            var fd = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: fd
            })
                .then(function(res) {
                    return res.text().then(function(text) {
                        var data = {};
                        try {
                            data = text ? JSON.parse(text) : {};
                        } catch (err) {
                            data = { message: 'Unexpected response from server.' };
                        }
                        return { ok: res.ok, status: res.status, data: data };
                    });
                })
                .then(function(result) {
                    if (!result.ok) {
                        if (btn) btn.disabled = false;
                        var msg = (result.data && result.data.message) ? result.data.message : 'Could not add to cart.';
                        if (result.data && result.data.errors) {
                            var first = Object.values(result.data.errors)[0];
                            if (first && first[0]) msg = first[0];
                        }
                        showToast(msg);
                        return;
                    }
                    runFly().then(function() {
                        if (btn) btn.disabled = false;
                        if (result.data.cart_count !== undefined) {
                            updateCartBadge(result.data.cart_count);
                        }
                        showToast(result.data.message || 'Added to cart');
                    });
                })
                .catch(function() {
                    if (btn) btn.disabled = false;
                    showToast('Network error. Try again.');
                });
        });
    });
})();
