/* ==========================================================================
   KSP - COOKIES
   --------------------------------------------------------------------------
   Tugas file ini:
   1. Helper kecil untuk baca/tulis cookie preferensi UI (window.KspCookies).
   2. Logika tombol "Terima" / "Tolak" pada banner cookie consent.

   Yang BOLEH disimpan di cookie (allowlist, tidak ada data lain):
     - ksp_cookie_consent : accepted | rejected
     - ksp_sidebar_state  : open | collapsed

   Tidak ada password, PIN, token, ID user, atau data autentikasi di sini.
   Cookie login (laravel_session) tetap dikelola Laravel di sisi server.
   ========================================================================== */
(function (window, document) {
    'use strict';

    if (window.KspCookies) {
        return;
    }

    var CONSENT_COOKIE = 'ksp_cookie_consent';
    var DURATION_DAYS = 30; // masa berlaku consent & preferensi

    // Nama cookie => nilai yang diizinkan.
    var ALLOWED = {
        ksp_cookie_consent: ['accepted', 'rejected'],
        ksp_sidebar_state: ['open', 'collapsed']
    };

    // Cookie preferensi: hanya disimpan jika pengguna menekan "Terima".
    var PREFERENCE_COOKIES = ['ksp_sidebar_state'];

    function isAllowed(name, value) {
        return Object.prototype.hasOwnProperty.call(ALLOWED, name) &&
            ALLOWED[name].indexOf(value) !== -1;
    }

    function readRaw(name) {
        var prefix = name + '=';
        var parts = document.cookie ? document.cookie.split('; ') : [];

        for (var i = 0; i < parts.length; i++) {
            if (parts[i].indexOf(prefix) === 0) {
                try {
                    return decodeURIComponent(parts[i].substring(prefix.length));
                } catch (e) {
                    return null;
                }
            }
        }

        return null;
    }

    function write(name, value) {
        var cookie = name + '=' + encodeURIComponent(value) +
            '; Max-Age=' + (DURATION_DAYS * 24 * 60 * 60) +
            '; Path=/; SameSite=Lax';

        if (window.location.protocol === 'https:') {
            cookie += '; Secure';
        }

        document.cookie = cookie;
    }

    function remove(name) {
        document.cookie = name + '=; Max-Age=0; Path=/; SameSite=Lax';
    }

    /* ---------- API publik ---------- */

    // Mengembalikan 'accepted', 'rejected', atau null (belum memilih).
    function getConsent() {
        var value = readRaw(CONSENT_COOKIE);
        return isAllowed(CONSENT_COOKIE, value) ? value : null;
    }

    function setConsent(status) {
        if (!isAllowed(CONSENT_COOKIE, status)) {
            return false;
        }

        write(CONSENT_COOKIE, status);

        // Jika menolak, hapus cookie preferensi yang mungkin sudah ada.
        if (status === 'rejected') {
            PREFERENCE_COOKIES.forEach(remove);
        }

        return true;
    }

    // Simpan preferensi UI. Hanya jalan jika consent = accepted.
    function setPreference(name, value) {
        if (PREFERENCE_COOKIES.indexOf(name) === -1) {
            return false;
        }

        if (!isAllowed(name, value) || getConsent() !== 'accepted') {
            return false;
        }

        write(name, value);
        return true;
    }

    function getPreference(name) {
        if (PREFERENCE_COOKIES.indexOf(name) === -1 || getConsent() !== 'accepted') {
            return null;
        }

        var value = readRaw(name);
        return isAllowed(name, value) ? value : null;
    }

    window.KspCookies = {
        getConsent: getConsent,
        setConsent: setConsent,
        setPreference: setPreference,
        getPreference: getPreference
    };

    /* ---------- Banner cookie consent ---------- */

    function hideBanner(banner) {
        banner.classList.add('is-hiding');

        window.setTimeout(function () {
            if (banner.parentNode) {
                banner.parentNode.removeChild(banner);
            }
        }, 250);
    }

    function onChoice(event) {
        var button = event.target.closest
            ? event.target.closest('[data-cookie-consent]')
            : null;

        if (!button) {
            return;
        }

        var status = button.getAttribute('data-cookie-consent');

        if (!setConsent(status)) {
            return;
        }

        var banner = document.getElementById('ksp-cookie-banner');
        if (banner) {
            hideBanner(banner);
        }

        // Beri tahu script lain (mis. responsive.js) agar bisa menyimpan preferensi.
        document.dispatchEvent(new CustomEvent('ksp:cookie-consent', {
            detail: { status: status }
        }));
    }

    document.addEventListener('click', onChoice);
})(window, document);
