document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('app-sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const overlay = document.getElementById('sidebar-overlay');


    /*
    |--------------------------------------------------------------------------
    | Check element
    |--------------------------------------------------------------------------
    */

    if (!sidebar || !toggle || !overlay) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Open sidebar
    |--------------------------------------------------------------------------
    */

    function openSidebar() {

        sidebar.classList.add('is-open');

        overlay.classList.add('is-visible');

        document.body.classList.add('sidebar-open');

        toggle.setAttribute(
            'aria-expanded',
            'true'
        );

        toggle.setAttribute(
            'aria-label',
            'Tutup menu navigasi'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Close sidebar
    |--------------------------------------------------------------------------
    */

    function closeSidebar() {

        sidebar.classList.remove('is-open');

        overlay.classList.remove('is-visible');

        document.body.classList.remove('sidebar-open');

        toggle.setAttribute(
            'aria-expanded',
            'false'
        );

        toggle.setAttribute(
            'aria-label',
            'Buka menu navigasi'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Hamburger
    |--------------------------------------------------------------------------
    */

    toggle.addEventListener(
        'click',
        function (event) {

            event.stopPropagation();

            if (sidebar.classList.contains('is-open')) {

                closeSidebar();

            } else {

                openSidebar();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Overlay
    |--------------------------------------------------------------------------
    */

    overlay.addEventListener(
        'click',
        function () {

            closeSidebar();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                closeSidebar();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Close sidebar setelah klik menu
    |--------------------------------------------------------------------------
    */

    sidebar
        .querySelectorAll('a')
        .forEach(function (link) {

            link.addEventListener(
                'click',
                function () {

                    if (window.innerWidth <= 1023) {

                        closeSidebar();

                    }

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Close ketika kembali ke desktop
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'resize',
        function () {

            if (window.innerWidth > 1023) {

                closeSidebar();

            }

        }
    );

});