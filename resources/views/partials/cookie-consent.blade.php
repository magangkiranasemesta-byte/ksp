{{--
    COOKIE CONSENT BANNER
    ---------------------------------------------------------------
    Banner hanya dirender jika cookie `ksp_cookie_consent` belum ada
    (atau nilainya tidak valid). Karena dicek di server, banner tidak
    "berkedip" saat halaman dimuat dan tidak muncul lagi selama cookie
    berlaku (30 hari).

    Tombol ditangani oleh public/js/cookies.js (atribut data-cookie-consent).
    CSS: public/css/responsive.css (bagian "COOKIE BANNER").
--}}
@php
    $kspConsentStatus = request()->cookie('ksp_cookie_consent');
@endphp

@unless (in_array($kspConsentStatus, ['accepted', 'rejected'], true))
    <div
        id="ksp-cookie-banner"
        class="ksp-cookie-banner"
        role="dialog"
        aria-live="polite"
        aria-labelledby="ksp-cookie-title"
        aria-describedby="ksp-cookie-desc">

        <div class="ksp-cookie-banner__inner">

            <div class="ksp-cookie-banner__text">
                <strong id="ksp-cookie-title" class="ksp-cookie-banner__title">
                    Pengaturan Cookies
                </strong>

                <p id="ksp-cookie-desc" class="ksp-cookie-banner__desc">
                    Sistem ini memakai cookie esensial agar login dan sesi Anda berjalan
                    dengan aman. Jika Anda menekan "Terima", kami juga mengingat preferensi
                    tampilan (misalnya sidebar tertutup) selama 30 hari. Password dan data
                    pribadi tidak pernah disimpan di cookie.
                </p>
            </div>

            <div class="ksp-cookie-banner__actions">
                <button
                    type="button"
                    class="ksp-cookie-btn ksp-cookie-btn--ghost"
                    data-cookie-consent="rejected">
                    Tolak
                </button>

                <button
                    type="button"
                    class="ksp-cookie-btn ksp-cookie-btn--primary"
                    data-cookie-consent="accepted">
                    Terima
                </button>
            </div>

        </div>
    </div>
@endunless
