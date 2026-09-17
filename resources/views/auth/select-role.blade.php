<!DOCTYPE html>
<html lang="id" class="dark bg-[#080d1a]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Akses - Maintenance System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-[#0d0714] via-[#0f172a] to-[#1e0a24] text-slate-100 min-h-screen flex flex-col justify-between p-6 relative overflow-x-hidden antialiased">

    <!-- Glowing Background Lights -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-indigo-600/15 blur-[140px] pointer-events-none -z-10 rounded-full"></div>
    <div class="fixed bottom-0 right-10 w-[500px] h-[300px] bg-blue-600/10 blur-[140px] pointer-events-none -z-10 rounded-full"></div>

    <!-- Navigation Header / Top Bar -->
    <div class="w-full max-w-7xl mx-auto flex justify-end items-center gap-3 z-20 pt-2">
        <!-- Tombol Logout & Kembali ke Login -->
        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
            @csrf
            <button type="submit" 
                    class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-400 to-rose-400 hover:from-red-500 hover:to-rose-500 text-white font-semibold text-xs tracking-wide shadow-lg shadow-red-900/40 transition-all duration-200 hover:scale-105 border border-red-400/30 cursor-pointer">
                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                <span>Logout & Kembali ke Login</span>
                <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
            </button>
        </form>

        <!-- Tombol Khusus Akses Dashboard Superadmin -->
        <a href="{{ route('dashboard', ['switch_role' => 'SUPERADMIN']) }}" 
        class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-400 to-rose-400 hover:from-red-500 hover:to-rose-500 text-white font-semibold text-xs tracking-wide shadow-lg shadow-red-900/40 transition-all duration-200 hover:scale-105 border border-red-400/30">
            <i class="fa-solid fa-user-shield text-sm"></i>
            <span>Dashboard Superadmin</span>
            <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
        </a>
    </div>

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col items-center justify-center my-6 z-10">
        
        <!-- Header Logo & Title -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 mb-4 shadow-lg shadow-indigo-500/10 backdrop-blur-md">
                <i class="fa-solid fa-gears text-3xl"></i>
            </div>
            <p class="text-[11px] uppercase tracking-widest text-indigo-400 font-bold mb-1">MAINTENANCE SYSTEM</p>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-blue-400">
                Selamat <span class="text-indigo-400">Datang</span> Kembali!
            </h1>
            <p class="text-slate-400 text-xs md:text-sm mt-2">Silakan pilih jenis akses Anda untuk melanjutkan ke sistem</p>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl w-full">
            
            <!-- Option 1: Users -->
            <div class="bg-slate-900/90 backdrop-blur-xl border border-purple-500/30 hover:border-purple-500/70 rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-2 shadow-2xl shadow-purple-950/30 group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-500/10 border border-purple-500/30 text-purple-400 flex items-center justify-center mb-5 mx-auto group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-purple-300 mb-2">Users</h3>
                    <p class="text-xs text-slate-400 text-center mb-6 leading-relaxed">Akses umum untuk melihat informasi dan sistem</p>
                    
                    <hr class="border-slate-800/80 mb-5">
                    
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li class="flex items-center gap-2.5"><i class="fa-regular fa-eye text-purple-400"></i> Lihat Informasi</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-border-all text-purple-400"></i> Dashboard Pribadi</li>
                    </ul>
                </div>

                <a href="{{ route('dashboard', ['switch_role' => 'USER']) }}" class="mt-8 w-full py-2.5 px-4 bg-purple-500 hover:bg-purple-400 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-lg shadow-purple-500/40">
                    <span class="text-white">Masuk Sebagai Users</span> 
                    <i class="fa-solid fa-arrow-right text-[10px] text-white"></i>
                </a>
            </div>

            <!-- Option 2: Engineer -->
            <div class="bg-slate-900/90 backdrop-blur-xl border border-blue-500/30 hover:border-blue-500/70 rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-2 shadow-2xl shadow-blue-950/30 group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/30 text-blue-400 flex items-center justify-center mb-5 mx-auto group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user-gear text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-blue-300 mb-2">Engineer</h3>
                    <p class="text-xs text-slate-400 text-center mb-6 leading-relaxed">Kelola maintenance request dan pekerjaan</p>
                    
                    <hr class="border-slate-800/80 mb-5">
                    
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li class="flex items-center gap-2.5"><i class="fa-regular fa-pen-to-square text-blue-400"></i> Buat Request</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-list-check text-blue-400"></i> Kelola Pekerjaan</li>
                        <li class="flex items-center gap-2.5"><i class="fa-regular fa-file-lines text-blue-400"></i> Laporan Maintenance</li>
                    </ul>
                </div>

                <a href="{{ route('dashboard', ['switch_role' => 'ENGINEER']) }}" class="mt-8 w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold flex items-center justify-center gap-2 transition-all shadow-lg shadow-blue-600/30">
                    <span class="text-white">Masuk Sebagai Engineer</span> 
                    <i class="fa-solid fa-arrow-right text-[10px] text-white"></i>
                </a>
            </div>

            <!-- Option 3: Supervisor -->
            <div class="bg-slate-900/90 backdrop-blur-xl border border-emerald-500/30 hover:border-emerald-500/70 rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-2 shadow-2xl shadow-emerald-950/30 group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mb-5 mx-auto group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-shield-halved text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-emerald-300 mb-2">Supervisor</h3>
                    <p class="text-xs text-slate-400 text-center mb-6 leading-relaxed">Review, approval dan monitoring pekerjaan</p>
                    
                    <hr class="border-slate-800/80 mb-5">
                    
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li class="flex items-center gap-2.5"><i class="fa-regular fa-square-check text-emerald-400"></i> Review Request</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Approval Pekerjaan</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-chart-line text-emerald-400"></i> Monitoring Progress</li>
                    </ul>
                </div>

                <a href="{{ route('dashboard', ['switch_role' => 'SUPERVISOR']) }}" class="mt-8 w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/30">
                    <span class="text-white">Masuk Sebagai Supervisor</span> 
                    <i class="fa-solid fa-arrow-right text-[10px] text-white"></i>
                </a>
            </div>

            <!-- Option 4: Manager -->
            <div class="bg-slate-900/90 backdrop-blur-xl border border-amber-500/30 hover:border-amber-500/70 rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-2 shadow-2xl shadow-amber-950/30 group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center mb-5 mx-auto group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-briefcase text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-amber-300 mb-2">Manager</h3>
                    <p class="text-xs text-slate-400 text-center mb-6 leading-relaxed">Kelola sistem, laporan dan analisis data</p>
                    
                    <hr class="border-slate-800/80 mb-5">
                    
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-chart-pie text-amber-400"></i> Laporan & Analisis</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-user-group text-amber-400"></i> Manajemen Tim</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-sliders text-amber-400"></i> Pengaturan Sistem</li>
                    </ul>
                </div>

                <a href="{{ route('dashboard', ['switch_role' => 'MANAGER']) }}" class="mt-8 w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-semibold flex items-center justify-center gap-2 transition-all shadow-lg shadow-amber-600/30">
                    <span class="text-white">Masuk Sebagai Manager</span> 
                    <i class="fa-solid fa-arrow-right text-[10px] text-white"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Footer Stats Widget -->
    <div class="bg-slate-900/80 border border-slate-800 backdrop-blur-xl rounded-2xl py-3.5 px-8 max-w-4xl w-full mx-auto flex flex-wrap items-center justify-around gap-4 text-xs z-10 shadow-2xl">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/30 text-blue-400 flex items-center justify-center">
                <i class="fa-solid fa-shield text-xs"></i>
            </div>
            <div>
                <div class="font-semibold text-slate-200">Sistem Aman & Terpercaya</div>
                <div class="text-[10px] text-slate-400">Data Anda dilindungi dengan enkripsi tingkat tinggi</div>
            </div>
        </div>

        <div class="h-6 w-px bg-slate-800 hidden md:block"></div>

        <div class="text-center">
            <div class="font-bold text-slate-200 text-sm">99.9%</div>
            <div class="text-[10px] text-slate-400">Uptime</div>
        </div>

        <div class="h-6 w-px bg-slate-800 hidden md:block"></div>

        <div class="flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-blue-400"></i>
            <div>
                <div class="font-semibold text-slate-200">SSL</div>
                <div class="text-[10px] text-slate-400">Secure</div>
            </div>
        </div>

        <div class="h-6 w-px bg-slate-800 hidden md:block"></div>

        <div class="flex items-center gap-2">
            <i class="fa-solid fa-cloud-arrow-up text-blue-400"></i>
            <div>
                <div class="font-semibold text-slate-200">Backup</div>
                <div class="text-[10px] text-slate-400">Daily</div>
            </div>
        </div>
    </div>

</body>
</html>