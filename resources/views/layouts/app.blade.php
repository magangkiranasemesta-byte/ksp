<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Maintenance X')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')

</head>

<body class="h-full font-sans antialiased text-slate-800">

    @auth

        <div class="flex min-h-screen bg-slate-100">

            <!-- =========================================================
                 SIDEBAR
            ========================================================== -->

            <aside
                class="w-64 bg-[#0B132B] text-white flex flex-col justify-between
                       p-4 shrink-0 shadow-xl max-h-screen sticky top-0
                       overflow-y-auto">

                <div>

                    <!-- Logo / Brand -->

                    <div class="flex items-center gap-3 pb-6 border-b border-slate-800">

                        <div
                            class="w-10 h-10 rounded-xl bg-blue-600
                                   flex items-center justify-center
                                   font-bold text-lg text-white
                                   shadow-md shadow-blue-500/30">

                            MX

                        </div>

                        <div>

                            <b class="block leading-tight text-white tracking-wide">
                                Maintenance X
                            </b>

                            <small class="text-xs text-slate-400">
                                Equipment System
                            </small>

                        </div>

                    </div>


                    <!-- =================================================
                         NAVIGATION
                    ================================================== -->

                    <nav class="mt-6 flex flex-col gap-1.5">


                        <!-- Dashboard -->

                        @if(auth()->user()->hasPermission('dashboard'))

                            <a
                                href="{{ route('dashboard') }}"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl
                                       text-sm font-medium transition-all
                                       {{ request()->routeIs('dashboard')
                                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                            : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6">
                                    </path>

                                </svg>

                                Dashboard

                            </a>

                        @endif


                        <!-- Tickets -->

                        @if(auth()->user()->hasPermission('tickets'))

                            <a
                                href="{{ route('tickets.index') }}"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl
                                       text-sm font-medium transition-all
                                       {{ request()->routeIs('tickets.*')
                                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                            : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z">
                                    </path>

                                </svg>

                                Tickets

                            </a>

                        @endif


                        <!-- =================================================
                             MAINTENANCE
                        ================================================== -->

                        @if(auth()->user()->hasPermission('maintenance'))

                            @php

                                $isMaintenanceActive =
                                    request()->routeIs('maintenance.*')
                                    || request()->routeIs('maintenance.preventive.*')
                                    || request()->routeIs('downtime.*');

                            @endphp


                            <details
                                class="group border-none"
                                {{ $isMaintenanceActive ? 'open' : '' }}>

                                <summary
                                    class="flex items-center justify-between
                                           px-3.5 py-2.5 rounded-xl
                                           text-sm font-medium transition-all
                                           cursor-pointer list-none select-none
                                           text-slate-400
                                           hover:bg-slate-800/60
                                           hover:text-white
                                           {{ $isMaintenanceActive
                                                ? 'bg-slate-800/40 text-white'
                                                : '' }}">

                                    <div class="flex items-center gap-3">

                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 3a8 8 0 100 16 8 8 0 000-16zM21 21l-4.35-4.35">
                                            </path>

                                        </svg>

                                        <span>
                                            Maintenance
                                        </span>

                                    </div>


                                    <svg
                                        class="w-4 h-4 transition-transform
                                               group-open:rotate-180
                                               text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7">
                                        </path>

                                    </svg>

                                </summary>


                                <!-- Sub Menu -->

                                <div
                                    class="mt-1 ml-3 pl-3
                                           border-l border-slate-800
                                           flex flex-col gap-1">


                                    <!-- Maintenance Request -->

                                    <a
                                        href="{{ route('maintenance.index') }}"
                                        class="flex items-center gap-3
                                               px-3 py-2 rounded-lg
                                               text-xs font-medium
                                               transition-all
                                               {{ request()->routeIs('maintenance.index')
                                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30'
                                                    : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                        Maintenance Request

                                    </a>


                                    <!-- Preventive Maintenance -->

                                    <a
                                        href="{{ route('maintenance.preventive.index') }}"
                                        class="flex items-center gap-3
                                               px-3 py-2 rounded-lg
                                               text-xs font-medium
                                               transition-all
                                               {{ request()->routeIs('maintenance.preventive.*')
                                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30'
                                                    : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                        Preventive Maintenance

                                    </a>


                                    <!-- =================================================
                                         DOWNTIME EQUIPMENT
                                    ================================================== -->

                                    <a
                                        href="{{ route('downtime.index') }}"
                                        class="flex items-center gap-3
                                               px-3 py-2 rounded-lg
                                               text-xs font-medium
                                               transition-all
                                               {{ request()->routeIs('downtime.*')
                                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30'
                                                    : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                        <!-- Downtime Icon -->

                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>

                                        </svg>

                                        <span>
                                            Downtime Equipment
                                        </span>

                                    </a>


                                </div>

                            </details>

                        @endif


                        <!-- =================================================
                             HISTORY
                        ================================================== -->

                        @if(auth()->user()->hasPermission('history'))

                            <a
                                href="{{ route('history') }}"
                                class="flex items-center gap-3
                                       px-3.5 py-2.5 rounded-xl
                                       text-sm font-medium transition-all
                                       {{ request()->routeIs('history')
                                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                            : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>

                                </svg>

                                History

                            </a>

                        @endif


                        <!-- =================================================
                             EQUIPMENT
                        ================================================== -->

                        @if(auth()->user()->hasPermission('equipment'))

                            <a
                                href="{{ route('equipment.index') }}"
                                class="flex items-center gap-3
                                       px-3.5 py-2.5 rounded-xl
                                       text-sm font-medium transition-all
                                       {{ request()->routeIs('equipment.*')
                                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                            : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10.5 6h3m-7.5 5h15M6 18h12M8 3h8a2 2 0 012 2v14a2 2 0 01-2 2H8a2 2 0 01-2-2V5a2 2 0 012-2z">
                                    </path>

                                </svg>

                                Equipment

                            </a>

                        @endif


                        <!-- =================================================
                             SPAREPARTS
                        ================================================== -->

                        @if(auth()->user()->hasPermission('spareparts'))

                            <a
                                href="{{ route('spareparts.index') }}"
                                class="flex items-center gap-3
                                       px-3.5 py-2.5 rounded-xl
                                       text-sm font-medium transition-all
                                       {{ request()->routeIs('spareparts.*')
                                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                            : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7m16 0l-8 4m-8-4l8 4m0 0v10">
                                    </path>

                                </svg>

                                Spareparts

                            </a>

                        @endif


                        <!-- =================================================
                             USER MANAGEMENT
                        ================================================== -->

                        @if(auth()->user()->hasPermission('users'))

                            <a
                                href="{{ route('users.index') }}"
                                class="flex items-center gap-3
                                       px-3.5 py-2.5 rounded-xl
                                       text-sm font-medium transition-all
                                       {{ request()->routeIs('users.*')
                                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                            : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm7-3a4 4 0 110 8m4 5v-2a4 4 0 00-3-3.87">
                                    </path>

                                </svg>

                                User Management

                            </a>

                        @endif


                        <!-- =================================================
                             ACTIVITY LOGS
                        ================================================== -->

                        @if(
                            auth()->user()->hasPermission('activity_logs')
                            || auth()->user()->role === 'SUPERADMIN'
                        )

                            @if(Route::has('activity-logs.index'))

                                <a
                                    href="{{ route('activity-logs.index') }}"
                                    class="flex items-center gap-3
                                           px-3.5 py-2.5 rounded-xl
                                           text-sm font-medium transition-all
                                           {{ request()->routeIs('activity-logs.*')
                                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                                : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>

                                    </svg>

                                    Activity Logs

                                </a>

                            @endif

                        @endif

                    </nav>

                </div>


                <!-- =========================================================
                     PROFILE BOTTOM
                ========================================================== -->

                <div class="pt-4 mt-6 border-t border-slate-800">

                    <div class="flex items-center gap-3 mb-3">

                        <div
                            class="w-9 h-9 rounded-full
                                   bg-slate-800
                                   flex items-center justify-center
                                   font-bold text-sm
                                   text-slate-300
                                   border border-slate-700">

                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}

                        </div>


                        <div class="overflow-hidden">

                            <b class="block text-sm truncate text-slate-200">
                                {{ auth()->user()->username }}
                            </b>

                            <small class="text-xs text-slate-400 block capitalize">
                                {{ auth()->user()->role }}
                            </small>

                        </div>

                    </div>


                    <!-- Logout -->

                    <form
                        method="POST"
                        action="{{ route('logout') }}">

                        @csrf

                        <button
                            type="submit"
                            class="w-full text-center
                                   py-2 px-3 rounded-xl
                                   border border-slate-800
                                   text-xs font-semibold
                                   text-slate-300
                                   hover:bg-red-500/10
                                   hover:text-red-400
                                   hover:border-red-500/30
                                   transition-all">

                            Logout

                        </button>

                    </form>

                </div>

            </aside>


            <!-- =========================================================
                 MAIN AREA
            ========================================================== -->

            <main class="flex-1 flex flex-col min-w-0">


                <!-- HEADER -->

                <header
                    class="bg-white border-b border-slate-200
                           px-8 py-4
                           flex items-center justify-between">

                    <div>

                        <span
                            class="text-xs font-bold
                                   text-blue-600
                                   uppercase
                                   tracking-widest">

                            EQUIPMENT MAINTENANCE

                        </span>


                        <h1
                            class="text-2xl font-bold text-slate-900">

                            @yield('page_title', 'Dashboard')

                        </h1>

                    </div>


                    <!-- User Information -->

                    <div class="text-right">

                        <span
                            class="font-semibold
                                   text-slate-700
                                   block text-sm">

                            {{ auth()->user()->username }}

                        </span>


                        <span
                            class="inline-block
                                   px-2.5 py-0.5
                                   text-[10px]
                                   font-extrabold
                                   uppercase
                                   rounded-md
                                   bg-blue-100
                                   text-blue-700
                                   tracking-wider">

                            {{ auth()->user()->role }}

                        </span>

                    </div>

                </header>


                <!-- =====================================================
                     PAGE CONTENT
                ====================================================== -->

                <section class="p-8 flex-1">


                    <!-- SUCCESS MESSAGE -->

                    @if(session('success'))

                        <div
                            class="alert mb-6
                                   p-4 rounded-xl
                                   bg-emerald-50
                                   text-emerald-700
                                   border border-emerald-200
                                   text-sm font-medium
                                   flex items-center justify-between">

                            <span>
                                {{ session('success') }}
                            </span>

                        </div>

                    @endif


                    <!-- ERROR MESSAGE -->

                    @if(session('error'))

                        <div
                            class="alert mb-6
                                   p-4 rounded-xl
                                   bg-red-50
                                   text-red-700
                                   border border-red-200
                                   text-sm font-medium
                                   flex items-center justify-between">

                            <span>
                                {{ session('error') }}
                            </span>

                        </div>

                    @endif


                    <!-- VALIDATION ERRORS -->

                    @if($errors->any())

                        <div
                            class="alert mb-6
                                   p-4 rounded-xl
                                   bg-red-50
                                   text-red-700
                                   border border-red-200
                                   text-sm">

                            <div class="font-semibold mb-2">
                                Terjadi kesalahan:
                            </div>

                            <ul class="list-disc ml-5 space-y-1">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    @yield('content')

                </section>

            </main>

        </div>

    @else

        @yield('content')

    @endauth


    <!-- ================================================================
         ALERT AUTO DISMISS
    ================================================================= -->

    <script>

        document.querySelectorAll('.alert').forEach(function (element) {

            setTimeout(function () {

                element.style.transition = 'opacity 0.4s ease';

                element.style.opacity = '0';

                setTimeout(function () {

                    element.remove();

                }, 400);

            }, 4500);

        });

    </script>


    @stack('scripts')

</body>

</html>