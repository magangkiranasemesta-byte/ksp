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


                    <!-- =================================================
                         LOGO / BRAND
                    ================================================== -->

                    <div
                        class="flex items-center gap-3 pb-6
                               border-b border-slate-800">

                        <div
                            class="w-10 h-10 rounded-xl bg-blue-600
                                   flex items-center justify-center
                                   font-bold text-lg text-white
                                   shadow-md shadow-blue-500/30">

                            MX

                        </div>


                        <div>

                            <b
                                class="block leading-tight
                                       text-white tracking-wide">

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


                        <!-- =================================================
                             DASHBOARD
                        ================================================== -->

                        @if(auth()->user()->hasPermission('dashboard'))

                            <a
                                href="{{ route('dashboard') }}"
                                class="flex items-center gap-3
                                       px-3.5 py-2.5 rounded-xl
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



                        <!-- =================================================
                             TICKETS
                        ================================================== -->

                        @if(auth()->user()->hasPermission('tickets'))

                            <a
                                href="{{ route('tickets.index') }}"
                                class="flex items-center gap-3
                                       px-3.5 py-2.5 rounded-xl
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



                                <!-- SUB MENU -->

                                
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

                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5h6">
                                        </path>

                                    </svg>

                                    <span>
                                        Maintenance Request
                                    </span>

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

                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>

                                    </svg>

                                    <span>
                                        Preventive Maintenance
                                    </span>

                                </a>


                                <!-- Work Order -->

                                <a
                                    href="{{ route('work-orders.index') }}"
                                    class="flex items-center gap-3
                                        px-3 py-2 rounded-lg
                                        text-xs font-medium
                                        transition-all
                                        {{ request()->routeIs('work-orders.*')
                                                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30'
                                                : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7h-2M9 5a3 3 0 006 0M9 5h6M9 13h6m-6 4h4">
                                        </path>

                                    </svg>

                                    <span>
                                        Work Order
                                    </span>

                                </a>


                                <!-- Downtime Equipment -->

                                <a
                                    href="{{ route('downtime.index') }}"
                                    class="flex items-center gap-3
                                        px-3 py-2 rounded-lg
                                        text-xs font-medium
                                        transition-all
                                        {{ request()->routeIs('downtime.*')
                                                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30'
                                                : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">

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

                <div
                    class="pt-4 mt-6
                           border-t border-slate-800">


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

                            <b
                                class="block text-sm
                                       truncate text-slate-200">

                                {{ auth()->user()->username }}

                            </b>


                            <small
                                class="text-xs
                                       text-slate-400
                                       block capitalize">

                                {{ auth()->user()->role }}

                            </small>

                        </div>

                    </div>



                    <!-- LOGOUT -->

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

            <main
                class="flex-1 flex flex-col
                       min-w-0">


                <!-- =====================================================
                     HEADER
                ====================================================== -->

                <header
                    class="bg-white
                           border-b border-slate-200
                           px-8 py-4
                           flex items-center
                           justify-between
                           relative
                           z-50">


                    <!-- =================================================
                         PAGE TITLE
                    ================================================== -->

                    <div>

                        <span
                            class="text-xs font-bold
                                   text-blue-600
                                   uppercase
                                   tracking-widest">

                            EQUIPMENT MAINTENANCE

                        </span>


                        <h1
                            class="text-2xl
                                   font-bold
                                   text-slate-900">

                            @yield('page_title', 'Dashboard')

                        </h1>

                    </div>



                    <!-- =================================================
                         HEADER RIGHT
                    ================================================== -->

                    <div
                        class="flex items-center
                               gap-4">


                        <!-- =============================================
                             NOTIFICATION CENTER
                        ============================================== -->

                        @php

                            $unreadNotificationCount =
                                auth()->user()
                                    ->unreadNotifications()
                                    ->count();

                            $latestNotifications =
                                auth()->user()
                                    ->notifications()
                                    ->latest()
                                    ->take(5)
                                    ->get();

                        @endphp


                        <div
                            class="relative"
                            id="notification-center">


                            <!-- =========================================
                                 BELL BUTTON
                            ========================================== -->

                            <button
                                type="button"
                                id="notification-toggle"
                                aria-label="Notifications"
                                class="
                                    relative
                                    w-10
                                    h-10
                                    rounded-lg
                                    flex
                                    items-center
                                    justify-center
                                    text-slate-500
                                    hover:bg-slate-100
                                    hover:text-slate-700
                                    transition
                                ">


                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="21"
                                    height="21"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path
                                        d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9">
                                    </path>

                                    <path
                                        d="M13.73 21a2 2 0 0 1-3.46 0">
                                    </path>

                                </svg>



                                <!-- =====================================
                                     NOTIFICATION BADGE
                                ====================================== -->

                                @if($unreadNotificationCount > 0)

                                    <span
                                        class="
                                            absolute
                                            top-0
                                            right-0
                                            min-w-[18px]
                                            h-[18px]
                                            px-1
                                            rounded-full
                                            bg-red-500
                                            text-white
                                            text-[10px]
                                            font-bold
                                            flex
                                            items-center
                                            justify-center
                                            border-2
                                            border-white
                                        "
                                    >

                                        {{
                                            $unreadNotificationCount > 99
                                                ? '99+'
                                                : $unreadNotificationCount
                                        }}

                                    </span>

                                @endif

                            </button>



                            <!-- =========================================
                                 NOTIFICATION DROPDOWN
                            ========================================== -->

                            <div
                                id="notification-dropdown"
                                class="
                                    hidden
                                    absolute
                                    right-0
                                    top-full
                                    mt-2
                                    w-[380px]
                                    bg-white
                                    rounded-xl
                                    border
                                    border-slate-200
                                    shadow-2xl
                                    z-[9999]
                                    overflow-hidden
                                ">


                                <!-- =====================================
                                     DROPDOWN HEADER
                                ====================================== -->

                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        px-4
                                        py-3
                                        border-b
                                        border-slate-100
                                    "
                                >

                                    <div>

                                        <h3
                                            class="
                                                font-semibold
                                                text-slate-900
                                                text-sm
                                            "
                                        >
                                            Notifications
                                        </h3>


                                        <p
                                            class="
                                                text-xs
                                                text-slate-400
                                                mt-0.5
                                            "
                                        >

                                            {{ $unreadNotificationCount }}
                                            belum dibaca

                                        </p>

                                    </div>


                                    <a
                                        href="{{ route('notifications.index') }}"
                                        class="
                                            text-xs
                                            font-medium
                                            text-blue-600
                                            hover:text-blue-800
                                        "
                                    >

                                        Lihat Semua

                                    </a>

                                </div>



                                <!-- =====================================
                                     NOTIFICATION LIST
                                ====================================== -->

                                <div
                                    class="
                                        max-h-[420px]
                                        overflow-y-auto
                                    "
                                >

                                    @forelse(
                                        $latestNotifications
                                        as $notification
                                    )

                                        @php

                                            $data =
                                                $notification->data;

                                            $isUnread =
                                                is_null(
                                                    $notification->read_at
                                                );

                                        @endphp



                                        @if($isUnread)


                                            <!-- =========================
                                                 UNREAD NOTIFICATION
                                            ========================== -->

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'notifications.read',
                                                    $notification->id
                                                ) }}"
                                            >

                                                @csrf


                                                <button
                                                    type="submit"
                                                    class="
                                                        w-full
                                                        text-left
                                                        px-4
                                                        py-3
                                                        border-b
                                                        border-slate-100
                                                        bg-blue-50/40
                                                        hover:bg-blue-50
                                                        transition
                                                    "
                                                >

                                                    <div
                                                        class="
                                                            flex
                                                            gap-3
                                                        "
                                                    >


                                                        <!-- ICON -->

                                                        <div
                                                            class="
                                                                w-9
                                                                h-9
                                                                rounded-full
                                                                bg-emerald-50
                                                                flex
                                                                items-center
                                                                justify-center
                                                                text-emerald-500
                                                                shrink-0
                                                            "
                                                        >

                                                            ✓

                                                        </div>



                                                        <!-- CONTENT -->

                                                        <div
                                                            class="
                                                                flex-1
                                                                min-w-0
                                                            "
                                                        >

                                                            <p
                                                                class="
                                                                    text-sm
                                                                    font-semibold
                                                                    text-slate-800
                                                                "
                                                            >

                                                                {{
                                                                    $data['title']
                                                                    ?? 'Notification'
                                                                }}

                                                            </p>


                                                            <p
                                                                class="
                                                                    text-xs
                                                                    text-slate-500
                                                                    mt-1
                                                                "
                                                            >

                                                                {{
                                                                    $data['message']
                                                                    ?? ''
                                                                }}

                                                            </p>


                                                            <p
                                                                class="
                                                                    text-[10px]
                                                                    text-slate-400
                                                                    mt-2
                                                                "
                                                            >

                                                                {{
                                                                    $notification
                                                                        ->created_at
                                                                        ->diffForHumans()
                                                                }}

                                                            </p>

                                                        </div>



                                                        <!-- UNREAD DOT -->

                                                        <span
                                                            class="
                                                                w-2
                                                                h-2
                                                                rounded-full
                                                                bg-blue-500
                                                                mt-2
                                                                shrink-0
                                                            "
                                                        ></span>

                                                    </div>

                                                </button>

                                            </form>


                                        @else


                                            <!-- =========================
                                                 READ NOTIFICATION
                                            ========================== -->

                                            <a
                                                href="{{ route('notifications.index') }}"
                                                class="
                                                    block
                                                    px-4
                                                    py-3
                                                    border-b
                                                    border-slate-100
                                                    hover:bg-slate-50
                                                    transition
                                                "
                                            >

                                                <div
                                                    class="
                                                        flex
                                                        gap-3
                                                    "
                                                >


                                                    <div
                                                        class="
                                                            w-9
                                                            h-9
                                                            rounded-full
                                                            bg-slate-100
                                                            flex
                                                            items-center
                                                            justify-center
                                                            shrink-0
                                                        "
                                                    >

                                                        🔔

                                                    </div>


                                                    <div>

                                                        <p
                                                            class="
                                                                text-sm
                                                                font-medium
                                                                text-slate-700
                                                            "
                                                        >

                                                            {{
                                                                $data['title']
                                                                ?? 'Notification'
                                                            }}

                                                        </p>


                                                        <p
                                                            class="
                                                                text-xs
                                                                text-slate-400
                                                                mt-1
                                                            "
                                                        >

                                                            {{
                                                                $data['message']
                                                                ?? ''
                                                            }}

                                                        </p>


                                                        <p
                                                            class="
                                                                text-[10px]
                                                                text-slate-400
                                                                mt-2
                                                            "
                                                        >

                                                            {{
                                                                $notification
                                                                    ->created_at
                                                                    ->diffForHumans()
                                                            }}

                                                        </p>

                                                    </div>

                                                </div>

                                            </a>

                                        @endif

                                    @empty


                                        <!-- =============================
                                             EMPTY NOTIFICATION
                                        ============================== -->

                                        <div
                                            class="
                                                py-10
                                                text-center
                                            "
                                        >

                                            <div
                                                class="
                                                    text-3xl
                                                    mb-2
                                                "
                                            >

                                                🔔

                                            </div>


                                            <p
                                                class="
                                                    text-sm
                                                    text-slate-500
                                                "
                                            >

                                                Tidak ada notifikasi

                                            </p>

                                        </div>

                                    @endforelse

                                </div>



                                <!-- =====================================
                                     DROPDOWN FOOTER
                                ====================================== -->

                                @if($latestNotifications->count() > 0)

                                    <div
                                        class="
                                            border-t
                                            border-slate-100
                                        "
                                    >

                                        <a
                                            href="{{ route('notifications.index') }}"
                                            class="
                                                block
                                                text-center
                                                py-3
                                                text-sm
                                                font-medium
                                                text-blue-600
                                                hover:bg-slate-50
                                                transition
                                            "
                                        >

                                            Lihat Semua Notifikasi

                                        </a>

                                    </div>

                                @endif

                            </div>

                        </div>



                        <!-- =================================================
                             USER INFORMATION
                        ================================================== -->

                        <div class="text-right">

                            <span
                                class="
                                    font-semibold
                                    text-slate-700
                                    block
                                    text-sm
                                "
                            >

                                {{ auth()->user()->username }}

                            </span>


                            <span
                                class="
                                    inline-block
                                    px-2.5
                                    py-0.5
                                    text-[10px]
                                    font-extrabold
                                    uppercase
                                    rounded-md
                                    bg-blue-100
                                    text-blue-700
                                    tracking-wider
                                "
                            >

                                {{ auth()->user()->role }}

                            </span>

                        </div>

                    </div>

                </header>



                <!-- =====================================================
                     PAGE CONTENT
                ====================================================== -->

                <section
                    class="
                        p-8
                        flex-1
                    "
                >


                    <!-- =================================================
                         SUCCESS MESSAGE
                    ================================================== -->

                    @if(session('success'))

                        <div
                            class="
                                alert
                                mb-6
                                p-4
                                rounded-xl
                                bg-emerald-50
                                text-emerald-700
                                border
                                border-emerald-200
                                text-sm
                                font-medium
                                flex
                                items-center
                                justify-between
                            "
                        >

                            <span>

                                {{ session('success') }}

                            </span>

                        </div>

                    @endif



                    <!-- =================================================
                         ERROR MESSAGE
                    ================================================== -->

                    @if(session('error'))

                        <div
                            class="
                                alert
                                mb-6
                                p-4
                                rounded-xl
                                bg-red-50
                                text-red-700
                                border
                                border-red-200
                                text-sm
                                font-medium
                                flex
                                items-center
                                justify-between
                            "
                        >

                            <span>

                                {{ session('error') }}

                            </span>

                        </div>

                    @endif



                    <!-- =================================================
                         VALIDATION ERRORS
                    ================================================== -->

                    @if($errors->any())

                        <div
                            class="
                                alert
                                mb-6
                                p-4
                                rounded-xl
                                bg-red-50
                                text-red-700
                                border
                                border-red-200
                                text-sm
                            "
                        >

                            <div
                                class="
                                    font-semibold
                                    mb-2
                                "
                            >

                                Terjadi kesalahan:

                            </div>


                            <ul
                                class="
                                    list-disc
                                    ml-5
                                    space-y-1
                                "
                            >

                                @foreach($errors->all() as $error)

                                    <li>

                                        {{ $error }}

                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif



                    <!-- =================================================
                         PAGE CONTENT
                    ================================================== -->

                    @yield('content')

                </section>

            </main>

        </div>


    @else


        @yield('content')


    @endauth



    <!-- ================================================================
         JAVASCRIPT
    ================================================================= -->


    <script>


        /* ================================================================
           ALERT AUTO DISMISS
        ================================================================= */

        document
            .querySelectorAll('.alert')
            .forEach(function (element) {

                setTimeout(function () {

                    element.style.transition =
                        'opacity 0.4s ease';

                    element.style.opacity = '0';


                    setTimeout(function () {

                        element.remove();

                    }, 400);

                }, 4500);

            });



        /* ================================================================
           NOTIFICATION CENTER
        ================================================================= */

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                const toggle =
                    document.getElementById(
                        'notification-toggle'
                    );


                const dropdown =
                    document.getElementById(
                        'notification-dropdown'
                    );


                const container =
                    document.getElementById(
                        'notification-center'
                    );


                /* =============================================
                   CEK ELEMENT
                ============================================== */

                if (
                    !toggle ||
                    !dropdown ||
                    !container
                ) {

                    return;

                }



                /* =============================================
                   OPEN / CLOSE DROPDOWN
                ============================================== */

                toggle.addEventListener(
                    'click',
                    function (event) {

                        event.stopPropagation();

                        dropdown.classList.toggle(
                            'hidden'
                        );

                    }
                );



                /* =============================================
                   CLICK OUTSIDE
                ============================================== */

                document.addEventListener(
                    'click',
                    function (event) {

                        if (
                            !container.contains(
                                event.target
                            )
                        ) {

                            dropdown.classList.add(
                                'hidden'
                            );

                        }

                    }
                );



                /* =============================================
                   ESC KEY
                ============================================== */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Escape'
                        ) {

                            dropdown.classList.add(
                                'hidden'
                            );

                        }

                    }
                );

            }
        );

    </script>



    @stack('scripts')


</body>

</html>