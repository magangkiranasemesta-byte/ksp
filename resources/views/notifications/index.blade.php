@extends('layouts.app')

@section('title', 'Notification Center')
@section('page_title', 'Notification Center')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">

        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Notification Center
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Semua pemberitahuan aktivitas sistem.
            </p>
        </div>

        @if(auth()->user()->unreadNotifications()->exists())

            <form
                method="POST"
                action="{{ route('notifications.read-all') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm hover:bg-slate-800"
                >
                    Tandai Semua Dibaca
                </button>
            </form>

        @endif

    </div>


    {{-- NOTIFICATION LIST --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        @forelse($notifications as $notification)

            @php
                $data = $notification->data;

                $title = $data['title'] ?? 'Notification';

                $message = $data['message'] ?? '';

                $type = $data['type'] ?? 'info';

                $url = $data['url'] ?? null;

                $isUnread = is_null($notification->read_at);
            @endphp


            <div
                class="
                    flex gap-4 p-5
                    border-b border-slate-100
                    last:border-b-0
                    {{ $isUnread ? 'bg-blue-50/40' : 'bg-white' }}
                "
            >

                {{-- ICON --}}
                <div class="flex-shrink-0">

                    @if($type === 'maintenance')

                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            🔧
                        </div>

                    @elseif($type === 'approval')

                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                            ✓
                        </div>

                    @elseif($type === 'success')

                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            ✓
                        </div>

                    @elseif($type === 'warning')

                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                            ⚠
                        </div>

                    @else

                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                            🔔
                        </div>

                    @endif

                </div>


                {{-- CONTENT --}}
                <div class="flex-1 min-w-0">

                    <div class="flex items-start justify-between gap-4">

                        <div>

                            <h3
                                class="
                                    text-sm
                                    {{ $isUnread
                                        ? 'font-bold text-slate-900'
                                        : 'font-semibold text-slate-700'
                                    }}
                                "
                            >
                                {{ $title }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $message }}
                            </p>

                            <span class="text-xs text-slate-400 mt-2 block">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>

                        </div>


                        {{-- STATUS --}}
                        @if($isUnread)

                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0 mt-1"></span>

                        @endif

                    </div>


                    {{-- ACTION --}}
                    @if($url)

                        <form
                            method="POST"
                            action="{{ route('notifications.read', $notification->id) }}"
                            class="mt-3"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="text-sm font-medium text-blue-600 hover:text-blue-800"
                            >
                                Lihat Detail →
                            </button>

                        </form>

                    @elseif($isUnread)

                        <form
                            method="POST"
                            action="{{ route('notifications.read', $notification->id) }}"
                            class="mt-3"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="text-sm font-medium text-blue-600 hover:text-blue-800"
                            >
                                Tandai Dibaca
                            </button>

                        </form>

                    @endif

                </div>

            </div>

        @empty

            <div class="py-16 text-center">

                <div class="text-4xl mb-3">
                    🔔
                </div>

                <h3 class="font-semibold text-slate-700">
                    Tidak ada notifikasi
                </h3>

                <p class="text-sm text-slate-400 mt-1">
                    Belum ada pemberitahuan untuk akun Anda.
                </p>

            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    @if($notifications->hasPages())

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>

    @endif

</div>

@endsection