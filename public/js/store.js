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
})();
