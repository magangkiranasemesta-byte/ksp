<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>403 - Error Cow Page</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            overflow: hidden;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #202025;
        }

        .error-page {
            width: 100%;
            height: 100vh;

            display: flex;
            flex-direction: column;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .header {
            height: 31vh;
            min-height: 190px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #202025;

            color: #ffffff;

            overflow: hidden;
        }

        .header h1 {
            font-size: clamp(30px, 5vw, 62px);

            font-weight: 900;

            font-style: italic;

            letter-spacing: -2px;

            text-align: center;

            animation: titleFloat 3s ease-in-out infinite;
        }

        /* =====================================================
           ORANGE SECTION
        ===================================================== */

        .main {
            position: relative;

            flex: 1;

            background: #ff8061;

            overflow: hidden;
        }

        /* =====================================================
           ERROR CONTENT
        ===================================================== */

        .error-content {
            position: absolute;

            left: 8%;

            top: 50%;

            transform: translateY(-50%);

            z-index: 20;
        }

        .error-number {
            font-size: clamp(110px, 16vw, 230px);

            line-height: .78;

            font-weight: 1000;

            color: #ffffff;

            letter-spacing: -14px;

            animation: errorShake 5s ease-in-out infinite;
        }

        .error-message {
            width: 300px;

            margin-top: 25px;

            color: #ffffff;

            font-size: clamp(17px, 2vw, 27px);

            line-height: 1.1;

            font-weight: 700;
        }

        /* =====================================================
           LOGIN BUTTON
        ===================================================== */

        .login-button {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            margin-top: 24px;

            padding: 13px 28px;

            min-width: 135px;

            border-radius: 5px;

            background: #ffd600;

            color: #1d1d1d;

            text-decoration: none;

            font-size: 16px;

            font-weight: 900;

            box-shadow:
                0 5px 0 rgba(0, 0, 0, .12);

            transition:
                .2s ease;
        }

        .login-button:hover {
            transform: translateY(-3px);

            box-shadow:
                0 8px 0 rgba(0, 0, 0, .12);
        }

        .login-button:active {
            transform: translateY(2px);

            box-shadow:
                0 2px 0 rgba(0, 0, 0, .12);
        }

        /* =====================================================
           COW SVG
        ===================================================== */

        .cow-wrapper {
            position: absolute;

            left: 0;
            top: 0;

            width: 100%;
            height: 100%;

            pointer-events: none;

            z-index: 10;
        }

        .cow-svg {
            position: absolute;

            left: 0;

            bottom: 17%;

            width: min(250px, 25vw);

            height: auto;

            overflow: visible;
        }

        /* =====================================================
           HOLE
        ===================================================== */

        .hole {
            position: absolute;

            right: 12%;

            bottom: 17%;

            width: 190px;

            height: 34px;

            background: #171719;

            border-radius: 50%;

            z-index: 5;

            box-shadow:
                inset 0 5px 10px rgba(255,255,255,.03),
                0 5px 0 rgba(0,0,0,.08);
        }

        .hole::after {
            content: "";

            position: absolute;

            left: 20%;

            top: 8px;

            width: 60%;

            height: 9px;

            background: #000000;

            border-radius: 50%;

            opacity: .7;
        }

        /* =====================================================
           SVG ANIMATION
        ===================================================== */

        /*
            Tahapan animasi:

            0%   = keluar dari kiri
            12%  = mulai berjalan
            55%  = sampai depan lubang
            67%  = berhenti sebentar
            76%  = menunduk
            86%  = masuk lubang
            100% = berhenti
        */

        #cow {
            animation:
                cowMove 9s cubic-bezier(.45, 0, .55, 1) infinite;
            transform-box: fill-box;
            transform-origin: center bottom;
        }

        @keyframes cowMove {

            0% {
                transform:
                    translateX(-300px)
                    translateY(0)
                    rotate(0deg);
            }

            8% {
                transform:
                    translateX(-210px)
                    translateY(0)
                    rotate(0deg);
            }

            18% {
                transform:
                    translateX(-80px)
                    translateY(-3px)
                    rotate(-1deg);
            }

            30% {
                transform:
                    translateX(80px)
                    translateY(0)
                    rotate(1deg);
            }

            45% {
                transform:
                    translateX(260px)
                    translateY(-3px)
                    rotate(-1deg);
            }

            55% {
                transform:
                    translateX(410px)
                    translateY(0)
                    rotate(1deg);
            }

            /* sampai dekat lubang */

            62% {
                transform:
                    translateX(500px)
                    translateY(0)
                    rotate(0deg);
            }

            /* berhenti sebentar */

            68% {
                transform:
                    translateX(520px)
                    translateY(0)
                    rotate(0deg);
            }

            /* mulai menunduk */

            73% {
                transform:
                    translateX(535px)
                    translateY(8px)
                    rotate(8deg);
            }

            78% {
                transform:
                    translateX(548px)
                    translateY(20px)
                    rotate(18deg);
            }

            /* masuk ke lubang */

            84% {
                transform:
                    translateX(558px)
                    translateY(48px)
                    rotate(35deg)
                    scale(.9);
            }

            90% {
                transform:
                    translateX(565px)
                    translateY(70px)
                    rotate(55deg)
                    scale(.72);
            }

            96% {
                transform:
                    translateX(570px)
                    translateY(82px)
                    rotate(70deg)
                    scale(.5);
            }

            /* berhenti */

            100% {
                transform:
                    translateX(570px)
                    translateY(85px)
                    rotate(75deg)
                    scale(.45);
            }
        }

        /* =====================================================
           LEGS WALKING
        ===================================================== */

        .leg-front-1,
        .leg-front-2,
        .leg-back-1,
        .leg-back-2 {
            transform-box: fill-box;
            transform-origin: top center;
        }

        .leg-front-1 {
            animation: legOne .32s ease-in-out infinite alternate;
        }

        .leg-front-2 {
            animation: legTwo .32s ease-in-out infinite alternate;
        }

        .leg-back-1 {
            animation: legTwo .32s ease-in-out infinite alternate;
        }

        .leg-back-2 {
            animation: legOne .32s ease-in-out infinite alternate;
        }

        @keyframes legOne {
            from {
                transform: rotate(12deg);
            }

            to {
                transform: rotate(-12deg);
            }
        }

        @keyframes legTwo {
            from {
                transform: rotate(-12deg);
            }

            to {
                transform: rotate(12deg);
            }
        }

        /* =====================================================
           TAIL
        ===================================================== */

        .tail {
            transform-box: fill-box;
            transform-origin: right center;

            animation:
                tailMove .5s ease-in-out infinite alternate;
        }

        @keyframes tailMove {

            from {
                transform: rotate(-18deg);
            }

            to {
                transform: rotate(18deg);
            }
        }

        /* =====================================================
           TITLE ANIMATION
        ===================================================== */

        @keyframes titleFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        /* =====================================================
           ERROR NUMBER
        ===================================================== */

        @keyframes errorShake {

            0%,
            90%,
            100% {
                transform: translateX(0);
            }

            92% {
                transform: translateX(-4px);
            }

            94% {
                transform: translateX(4px);
            }

            96% {
                transform: translateX(-2px);
            }

            98% {
                transform: translateX(0);
            }
        }

        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 800px) {

            .header {
                height: 27vh;
            }

            .main {
                min-height: 73vh;
            }

            .error-content {
                left: 7%;

                top: 42%;
            }

            .error-number {
                font-size: 110px;

                letter-spacing: -8px;
            }

            .error-message {
                width: 230px;

                font-size: 18px;
            }

            .hole {
                right: 7%;

                width: 130px;
            }

            .cow-svg {
                width: 180px;

                bottom: 17%;
            }

            @keyframes cowMove {

                0% {
                    transform:
                        translateX(-220px)
                        translateY(0)
                        rotate(0);
                }

                55% {
                    transform:
                        translateX(45vw)
                        translateY(0)
                        rotate(0);
                }

                68% {
                    transform:
                        translateX(58vw)
                        translateY(0)
                        rotate(0);
                }

                76% {
                    transform:
                        translateX(64vw)
                        translateY(18px)
                        rotate(18deg);
                }

                88% {
                    transform:
                        translateX(68vw)
                        translateY(65px)
                        rotate(55deg)
                        scale(.6);
                }

                100% {
                    transform:
                        translateX(69vw)
                        translateY(78px)
                        rotate(70deg)
                        scale(.45);
                }
            }
        }

    </style>
</head>

<body>

<div class="error-page">

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="header">

        <h1>
            403 Error Cow Page
        </h1>

    </div>


    <!-- =====================================================
         MAIN ERROR
    ====================================================== -->

    <div class="main">

        <!-- ERROR TEXT -->

        <div class="error-content">

            <div class="error-number">
                403
            </div>

            <div class="error-message">

                @if ($exception->getMessage())
                    {{ $exception->getMessage() }}
                @else
                    Sorry, You don't have access there...
                @endif

            </div>

            <a
                href="{{ route('login') }}"
                class="login-button"
            >
                LOGIN
            </a>

        </div>


        <!-- =================================================
             HOLE
        ================================================== -->

        <div class="hole"></div>


        <!-- =================================================
             COW SVG
        ================================================== -->

        <div class="cow-wrapper">

            <svg
                class="cow-svg"
                viewBox="0 0 300 180"
                xmlns="http://www.w3.org/2000/svg"
                aria-label="Animated cow"
            >

                <g id="cow">

                    <!-- =====================================
                         TAIL
                    ====================================== -->

                    <g class="tail">

                        <path
                            d="M53 76
                               C30 65 22 75 14 91"
                            fill="none"
                            stroke="#171717"
                            stroke-width="7"
                            stroke-linecap="round"
                        />

                        <path
                            d="M13 90
                               C3 82 0 94 9 101
                               C17 106 23 96 13 90Z"
                            fill="#171717"
                        />

                    </g>


                    <!-- =====================================
                         BODY
                    ====================================== -->

                    <ellipse
                        cx="130"
                        cy="86"
                        rx="80"
                        ry="45"
                        fill="#ffffff"
                    />


                    <!-- =====================================
                         BLACK SPOTS
                    ====================================== -->

                    <ellipse
                        cx="92"
                        cy="67"
                        rx="31"
                        ry="19"
                        fill="#111111"
                        transform="rotate(-15 92 67)"
                    />

                    <ellipse
                        cx="145"
                        cy="96"
                        rx="25"
                        ry="19"
                        fill="#111111"
                        transform="rotate(18 145 96)"
                    />

                    <ellipse
                        cx="174"
                        cy="64"
                        rx="18"
                        ry="14"
                        fill="#111111"
                    />


                    <!-- =====================================
                         FRONT LEGS
                    ====================================== -->

                    <g class="leg-front-1">

                        <rect
                            x="170"
                            y="112"
                            width="13"
                            height="43"
                            rx="5"
                            fill="#ffffff"
                        />

                        <rect
                            x="168"
                            y="149"
                            width="17"
                            height="9"
                            rx="3"
                            fill="#111111"
                        />

                    </g>


                    <g class="leg-front-2">

                        <rect
                            x="145"
                            y="114"
                            width="13"
                            height="42"
                            rx="5"
                            fill="#ffffff"
                        />

                        <rect
                            x="143"
                            y="150"
                            width="17"
                            height="9"
                            rx="3"
                            fill="#111111"
                        />

                    </g>


                    <!-- =====================================
                         BACK LEGS
                    ====================================== -->

                    <g class="leg-back-1">

                        <rect
                            x="75"
                            y="112"
                            width="13"
                            height="43"
                            rx="5"
                            fill="#ffffff"
                        />

                        <rect
                            x="73"
                            y="149"
                            width="17"
                            height="9"
                            rx="3"
                            fill="#111111"
                        />

                    </g>


                    <g class="leg-back-2">

                        <rect
                            x="52"
                            y="108"
                            width="13"
                            height="45"
                            rx="5"
                            fill="#ffffff"
                        />

                        <rect
                            x="50"
                            y="148"
                            width="17"
                            height="9"
                            rx="3"
                            fill="#111111"
                        />

                    </g>


                    <!-- =====================================
                         NECK
                    ====================================== -->

                    <path
                        d="M182 68
                           C188 50 201 40 217 43
                           L232 74
                           L205 91
                           Z"
                        fill="#ffffff"
                    />


                    <!-- =====================================
                         HEAD
                    ====================================== -->

                    <ellipse
                        cx="228"
                        cy="52"
                        rx="39"
                        ry="34"
                        fill="#ffffff"
                        transform="rotate(12 228 52)"
                    />


                    <!-- =====================================
                         HEAD SPOT
                    ====================================== -->

                    <ellipse
                        cx="243"
                        cy="39"
                        rx="18"
                        ry="13"
                        fill="#111111"
                        transform="rotate(20 243 39)"
                    />


                    <!-- =====================================
                         EAR
                    ====================================== -->

                    <ellipse
                        cx="202"
                        cy="27"
                        rx="19"
                        ry="10"
                        fill="#111111"
                        transform="rotate(-20 202 27)"
                    />

                    <ellipse
                        cx="250"
                        cy="20"
                        rx="19"
                        ry="9"
                        fill="#111111"
                        transform="rotate(20 250 20)"
                    />


                    <!-- =====================================
                         HORNS
                    ====================================== -->

                    <path
                        d="M211 20
                           C203 4 215 0 223 10"
                        fill="none"
                        stroke="#ffffff"
                        stroke-width="6"
                        stroke-linecap="round"
                    />

                    <path
                        d="M251 14
                           C260 0 271 7 265 19"
                        fill="none"
                        stroke="#ffffff"
                        stroke-width="6"
                        stroke-linecap="round"
                    />


                    <!-- =====================================
                         MUZZLE
                    ====================================== -->

                    <ellipse
                        cx="254"
                        cy="67"
                        rx="22"
                        ry="17"
                        fill="#f2a3a3"
                    />


                    <!-- =====================================
                         NOSE
                    ====================================== -->

                    <ellipse
                        cx="247"
                        cy="67"
                        rx="3.5"
                        ry="4"
                        fill="#111111"
                    />

                    <ellipse
                        cx="261"
                        cy="67"
                        rx="3.5"
                        ry="4"
                        fill="#111111"
                    />


                    <!-- =====================================
                         EYE
                    ====================================== -->

                    <circle
                        cx="235"
                        cy="48"
                        r="5"
                        fill="#111111"
                    />

                    <circle
                        cx="237"
                        cy="46"
                        r="1.7"
                        fill="#ffffff"
                    />


                    <!-- =====================================
                         MOUTH
                    ====================================== -->

                    <path
                        d="M246 77
                           Q254 82 262 76"
                        fill="none"
                        stroke="#111111"
                        stroke-width="2"
                        stroke-linecap="round"
                    />

                </g>

            </svg>

        </div>

    </div>

</div>

</body>
</html>