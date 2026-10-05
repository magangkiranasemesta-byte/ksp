/* ==========================================================================
   KSP - RESPONSIVE SIDEBAR
   --------------------------------------------------------------------------
   Desktop (>= 1024px)  : tombol hamburger menutup/membuka sidebar.
                          Kondisi disimpan di cookie ksp_sidebar_state
                          (hanya jika pengguna sudah menekan "Terima").
   Tablet/Mobile        : sidebar menjadi drawer (off-canvas) + overlay.
                          Selalu tertutup saat halaman dimuat, tidak disimpan.

   Class awal pada <html> (ksp-sidebar-collapsed) dipasang oleh Blade
   di sisi server, sehingga tidak ada kedipan saat pindah halaman.
   ========================================================================== */
(function (window, document) {
    'use strict';

    var COOKIE_NAME = 'ksp_sidebar_state';
    var CLASS_COLLAPSED = 'ksp-sidebar-collapsed'; // desktop
    var CLASS_OPEN = 'ksp-sidebar-open';           // mobile/tablet
    var CLASS_NO_SCROLL = 'ksp-no-scroll';

    var root = document.documentElement;
    var desktop = window.matchMedia('(min-width: 1024px)');

    function init() {
        var sidebar = document.getElementById('ksp-sidebar');
        var toggle = document.getElementById('ksp-sidebar-toggle');
        var overlay = document.getElementById('ksp-sidebar-overlay');

        if (!sidebar || !toggle) {
            return;
        }

        function isCollapsed() {
            return root.classList.contains(CLASS_COLLAPSED);
        }

        function isDrawerOpen() {
            return root.classList.contains(CLASS_OPEN);
        }

        function syncAria() {
            var expanded = desktop.matches ? !isCollapsed() : isDrawerOpen();
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        }

        function savePreference() {
            if (window.KspCookies) {
                window.KspCookies.setPreference(
                    COOKIE_NAME,
                    isCollapsed() ? 'collapsed' : 'open'
                );
            }
        }

        function openDrawer() {
            root.classList.add(CLASS_OPEN, CLASS_NO_SCROLL);
            syncAria();
        }

        function closeDrawer() {
            root.classList.remove(CLASS_OPEN, CLASS_NO_SCROLL);
            syncAria();
        }

        toggle.addEventListener('click', function () {
            if (desktop.matches) {
                root.classList.toggle(CLASS_COLLAPSED);
                savePreference();
            } else if (isDrawerOpen()) {
                closeDrawer();
            } else {
                openDrawer();
            }

            syncAria();
        });

        if (overlay) {
            overlay.addEventListener('click', closeDrawer);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && isDrawerOpen()) {
                closeDrawer();
                toggle.focus();
            }
        });

        // Pindah ke ukuran desktop saat drawer terbuka -> tutup drawer.
        var onBreakpointChange = function () {
            if (desktop.matches) {
                closeDrawer();
            }
            syncAria();
        };

        if (desktop.addEventListener) {
            desktop.addEventListener('change', onBreakpointChange);
        } else if (desktop.addListener) {
            desktop.addListener(onBreakpointChange); // Safari lama
        }

        // Jika pengguna baru menekan "Terima", simpan kondisi sidebar saat ini.
        document.addEventListener('ksp:cookie-consent', function (event) {
            if (event.detail && event.detail.status === 'accepted') {
                savePreference();
            }
        });

        syncAria();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window, document);
