<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Yönetim') — Ahşap Evim Admin</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/ahsaplogo_yataybg.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/ahsaplogo_yataybg.png">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ── Sidebar transition ── */
        #adminSidebar {
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1),
                        width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Desktop collapsed state */
        body.sidebar-collapsed #adminSidebar {
            width: 4rem; /* 64px — icon only */
        }
        body.sidebar-collapsed #adminSidebar .nav-label,
        body.sidebar-collapsed #adminSidebar .brand-text,
        body.sidebar-collapsed #adminSidebar .logout-label {
            display: none;
        }
        body.sidebar-collapsed #adminSidebar .nav-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }
        body.sidebar-collapsed #adminSidebar .nav-item i {
            margin-right: 0;
        }
        body.sidebar-collapsed #adminSidebar .brand-logo {
            justify-content: center;
        }

        /* Mobile: sidebar hidden off-screen */
        @media (max-width: 767px) {
            #adminSidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                z-index: 50;
                transform: translateX(-100%);
                width: 16rem !important; /* always full width on mobile */
            }
            #adminSidebar.mobile-open {
                transform: translateX(0);
            }
            body.sidebar-collapsed #adminSidebar .nav-label,
            body.sidebar-collapsed #adminSidebar .brand-text,
            body.sidebar-collapsed #adminSidebar .logout-label {
                display: inline !important;
            }
            body.sidebar-collapsed #adminSidebar .nav-item {
                justify-content: flex-start !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            body.sidebar-collapsed #adminSidebar .nav-item i {
                margin-right: 0.5rem !important;
            }
        }

        /* Active nav link */
        .nav-item.active {
            background-color: #3D332B;
            border-left: 3px solid #C87A53;
        }

        /* Scrollbar for sidebar */
        #adminSidebar nav::-webkit-scrollbar { width: 4px; }
        #adminSidebar nav::-webkit-scrollbar-thumb { background: #3D332B; border-radius: 4px; }

        /* Table responsive improvements */
        .admin-table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* Card stack layout for very small screens */
        @media (max-width: 640px) {
            .responsive-stack thead { display: none; }
            .responsive-stack tr { display: block; border: 1px solid #e5e7eb; border-radius: 0.75rem; margin-bottom: 0.75rem; padding: 0.75rem; background: white; }
            .responsive-stack td { display: flex; justify-content: space-between; align-items: flex-start; padding: 0.35rem 0; border: none; font-size: 0.8rem; }
            .responsive-stack td::before { content: attr(data-label); font-weight: 700; color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; margin-right: 0.5rem; flex-shrink: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" id="adminBody">

    {{-- ── Overlay for mobile sidebar ── --}}
    <div id="sidebarOverlay"
         class="hidden fixed inset-0 bg-black/50 z-40 md:hidden"
         onclick="closeSidebar()"></div>

    {{-- ── Sidebar ── --}}
    <aside id="adminSidebar"
           class="w-64 bg-[#29221C] text-white flex flex-col flex-shrink-0 overflow-hidden">

        {{-- Brand --}}
        <div class="flex items-center brand-logo gap-3 px-4 py-4 bg-[#1D1713] border-b border-[#3D332B]">
            <div class="w-8 h-8 bg-[#C87A53] rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-tree text-white text-sm"></i>
            </div>
            <span class="brand-text font-bold text-base text-white leading-tight">Ahşap Evim<br><span class="text-[10px] font-normal text-amber-400 tracking-widest uppercase">Admin Panel</span></span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-3 space-y-0.5 px-2">

            {{-- Ana Yönetim --}}
            <p class="nav-label px-2 pt-3 pb-1 text-[9px] font-bold text-[#6B5C52] uppercase tracking-widest">Ana Yönetim</p>

            <a href="{{ route('admin.revenue.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->routeIs('admin.revenue*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-chart-line w-4 text-center text-amber-400"></i>
                <span class="nav-label">Gelir Tablosu</span>
            </a>

            <a href="{{ url('/yonetim/siparisler') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->is('yonetim/siparisler*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-shopping-bag w-4 text-center text-emerald-400"></i>
                <span class="nav-label">Siparişler</span>
            </a>

            {{-- Ürün Yönetimi --}}
            <p class="nav-label px-2 pt-4 pb-1 text-[9px] font-bold text-[#6B5C52] uppercase tracking-widest">Ürün Yönetimi</p>

            <a href="{{ url('/yonetim/urunler') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->is('yonetim/urunler*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-box w-4 text-center text-blue-400"></i>
                <span class="nav-label">Ürünler</span>
            </a>

            <a href="{{ url('/yonetim/kategoriler') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->is('yonetim/kategoriler*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-list w-4 text-center text-purple-400"></i>
                <span class="nav-label">Kategoriler</span>
            </a>

            <!-- 3D Şablonlar (Arka plana alındı, ihtiyaç durumunda hidden kaldırılarak aktif edilebilir) -->
            <a href="{{ url('/yonetim/3d-sablonlar') }}"
               class="hidden nav-item items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->is('yonetim/3d-sablonlar*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-cube w-4 text-center text-cyan-400"></i>
                <span class="nav-label">3D Şablonlar</span>
            </a>




            {{-- Kargo & İletişim --}}
            <p class="nav-label px-2 pt-4 pb-1 text-[9px] font-bold text-[#6B5C52] uppercase tracking-widest">Kargo & İletişim</p>

            <a href="{{ route('admin.shipping_companies.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->routeIs('admin.shipping_companies*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-truck-fast w-4 text-center text-orange-400"></i>
                <span class="nav-label">Kargo Şirketleri</span>
            </a>

            <a href="{{ route('admin.mail_logs.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->routeIs('admin.mail_logs*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-envelope-circle-check w-4 text-center text-amber-400"></i>
                <span class="nav-label">E-Posta Logları</span>
            </a>

            <a href="{{ route('admin.sms_logs.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->routeIs('admin.sms_logs*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-comment-sms w-4 text-center text-[#C87A53]"></i>
                <span class="nav-label">SMS Logları</span>
            </a>

            {{-- Site --}}
            <p class="nav-label px-2 pt-4 pb-1 text-[9px] font-bold text-[#6B5C52] uppercase tracking-widest">Site</p>

            <a href="{{ url('/yonetim/sayfalar') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->is('yonetim/sayfalar*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-file-lines w-4 text-center text-slate-400"></i>
                <span class="nav-label">Bilgilendirme Sayfaları</span>
            </a>

            <a href="{{ route('admin.banners.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->routeIs('admin.banners*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-images w-4 text-center text-[#C87A53]"></i>
                <span class="nav-label">Anasayfa Görselleri</span>
            </a>

            {{-- Sistem & Ayarlar --}}
            <p class="nav-label px-2 pt-4 pb-1 text-[9px] font-bold text-[#6B5C52] uppercase tracking-widest">Sistem</p>

            <a href="{{ route('admin.settings.index') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-300 hover:text-white {{ request()->routeIs('admin.settings*') ? 'active text-white' : '' }}">
                <i class="fa-solid fa-gear w-4 text-center text-teal-400"></i>
                <span class="nav-label">Sistem Ayarları</span>
            </a>

            <a href="{{ url('/') }}" target="_blank"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#3D332B] transition-colors text-sm font-medium text-gray-400 hover:text-white">
                <i class="fa-solid fa-arrow-up-right-from-square w-4 text-center text-gray-500"></i>
                <span class="nav-label">Siteyi Görüntüle</span>
            </a>
        </nav>

        {{-- Logout --}}
        <div class="border-t border-[#3D332B] px-2 py-3">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-red-950/50 text-red-400 hover:text-red-200 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-sign-out-alt w-4 text-center"></i>
                    <span class="logout-label">Güvenli Çıkış</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main Content ── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Header --}}
        <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between gap-4 flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-3">
                {{-- Hamburger / Toggle button --}}
                <button onclick="toggleSidebar()"
                        id="sidebarToggleBtn"
                        class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-[#29221C] hover:text-white text-gray-600 flex items-center justify-center transition-colors duration-200 flex-shrink-0"
                        title="Menüyü Aç/Kapat">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>

                {{-- Breadcrumb / Page title --}}
                <div>
                    <h1 class="text-base font-bold text-gray-800 leading-tight">@yield('header', 'Yönetim Paneli')</h1>
                    <p class="text-[11px] text-gray-400 hidden sm:block">Ahşap Evim Admin Paneli</p>
                </div>
            </div>

            {{-- Right side: quick actions & notification center --}}
            <div class="flex items-center gap-3">
                {{-- Live Orders Notification Center --}}
                <div class="relative" id="orderNotificationDropdownContainer">
                    <button onclick="toggleOrderNotifications()" id="notificationBellBtn"
                            class="relative w-9 h-9 rounded-lg bg-gray-100 hover:bg-amber-100/70 text-gray-700 hover:text-[#C87A53] flex items-center justify-center transition-all"
                            title="Sipariş Bildirimleri">
                        <i class="fa-solid fa-bell text-sm"></i>
                        <span id="orderNotificationBadge" class="hidden absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-xs">
                            0
                        </span>
                    </button>

                    {{-- Notification Dropdown --}}
                    <div id="orderNotificationMenu"
                         class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 overflow-hidden transform transition-all duration-200 origin-top-right">
                        <div class="p-3.5 bg-[#29221C] text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-bell text-amber-400 text-sm"></i>
                                <span class="font-bold text-xs">Sipariş Bildirimleri</span>
                            </div>
                            <span id="notificationDropdownCount" class="text-[10px] bg-[#C87A53] text-white px-2 py-0.5 rounded-full font-bold">0 Sipariş</span>
                        </div>

                        <div id="orderNotificationsList" class="max-h-80 overflow-y-auto divide-y divide-gray-100 text-xs">
                            <div class="p-6 text-center text-gray-400">
                                <i class="fa-solid fa-circle-notch fa-spin text-lg mb-2 block text-[#C87A53]"></i>
                                Bildirimler kontrol ediliyor...
                            </div>
                        </div>

                        <div class="p-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs">
                            <a href="{{ url('/yonetim/siparisler') }}" class="font-bold text-[#C87A53] hover:underline flex items-center gap-1">
                                Tüm Siparişler <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                            <button type="button" onclick="playNotificationChime()" title="Ses Testi" class="text-gray-400 hover:text-gray-600 text-[11px] flex items-center gap-1">
                                <i class="fa-solid fa-volume-high text-[#C87A53]"></i> Ses Testi
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Site link shortcut --}}
                <a href="{{ url('/') }}" target="_blank"
                   class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 hover:text-[#C87A53] transition px-3 py-1.5 rounded-lg hover:bg-amber-50 border border-transparent hover:border-amber-100">
                    <i class="fa-solid fa-store text-xs"></i>
                    <span>Siteyi Gör</span>
                </a>
                {{-- Admin badge --}}
                <div class="flex items-center gap-2 bg-[#1D1713] text-amber-400 rounded-lg px-3 py-1.5">
                    <div class="w-5 h-5 rounded-full bg-[#C87A53] flex items-center justify-center">
                        <i class="fa-solid fa-user text-white text-[9px]"></i>
                    </div>
                    <span class="text-xs font-bold hidden sm:inline">Admin</span>
                </div>
            </div>
        </header>

        {{-- Content area --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-6">

            {{-- Toast: Başarı --}}
            @if(session('success'))
                <div id="adminSuccessToast"
                     class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm mb-5 flex items-center justify-between text-xs font-bold">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1 ml-4">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Toast: Hata --}}
            @if(session('error'))
                <div id="adminErrorToast"
                     class="bg-gradient-to-r from-rose-50 to-red-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-sm mb-5 flex items-center justify-between text-xs font-bold">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-exclamation text-xs"></i>
                        </div>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1 ml-4">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="bg-gradient-to-r from-rose-50 to-red-50 border border-rose-200 text-rose-800 p-4 rounded-xl shadow-sm mb-5 text-xs">
                    <div class="font-bold mb-1.5 flex items-center gap-1.5 text-rose-900">
                        <i class="fa-solid fa-circle-exclamation"></i> Lütfen aşağıdaki hataları düzeltin:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // ── Sidebar State Management ──────────────────────────────────────────
        const sidebar       = document.getElementById('adminSidebar');
        const overlay       = document.getElementById('sidebarOverlay');
        const body          = document.getElementById('adminBody');
        const STORAGE_KEY   = 'adminSidebarCollapsed';
        const isMobile      = () => window.innerWidth < 768;

        // Restore desktop state from localStorage
        function initSidebar() {
            if (!isMobile()) {
                const collapsed = localStorage.getItem(STORAGE_KEY) === 'true';
                if (collapsed) body.classList.add('sidebar-collapsed');
            }
        }

        function toggleSidebar() {
            if (isMobile()) {
                // Mobile: slide overlay drawer
                const isOpen = sidebar.classList.contains('mobile-open');
                if (isOpen) {
                    closeSidebar();
                } else {
                    sidebar.classList.add('mobile-open');
                    overlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            } else {
                // Desktop: collapse/expand icon-only rail
                body.classList.toggle('sidebar-collapsed');
                const isCollapsed = body.classList.contains('sidebar-collapsed');
                localStorage.setItem(STORAGE_KEY, isCollapsed);
            }
        }

        function closeSidebar() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close mobile sidebar on resize to desktop
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                closeSidebar();
            }
        });

        // Auto-close success toasts after 4s
        setTimeout(() => {
            const t = document.getElementById('adminSuccessToast');
            if (t) { t.style.opacity = '0'; t.style.transition = 'opacity 0.5s'; setTimeout(() => t?.remove(), 500); }
        }, 4000);

        initSidebar();

        // ── Canlı Sipariş Bildirim Sistemi (Web Audio + AJAX Polling) ───────────
        let lastKnownOrderId = 0;
        let isFirstFetch = true;

        // Web Audio API ile melodik uyarı sesi (Harici dosya gerektirmez)
        function playNotificationChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                
                const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6 (Do majör neşeli çan melodisi)
                notes.forEach((freq, index) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + (index * 0.1));
                    
                    gain.gain.setValueAtTime(0.2, ctx.currentTime + (index * 0.1));
                    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + (index * 0.1) + 0.35);
                    
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    
                    osc.start(ctx.currentTime + (index * 0.1));
                    osc.stop(ctx.currentTime + (index * 0.1) + 0.4);
                });
            } catch (err) {
                console.warn('Web Audio error:', err);
            }
        }

        function showLiveOrderToast(order) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-50 max-w-sm bg-white border-2 border-[#C87A53] rounded-2xl shadow-2xl p-4 flex items-start gap-3.5 transform transition-all duration-300 translate-y-10 opacity-0';
            toast.innerHTML = 
                '<div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md">' +
                    '<i class="fa-solid fa-bag-shopping text-base animate-bounce"></i>' +
                '</div>' +
                '<div class="flex-1 min-w-0">' +
                    '<div class="text-[11px] font-bold text-[#C87A53] uppercase tracking-wider">🎉 Yeni Sipariş Geldi!</div>' +
                    '<div class="text-xs font-extrabold text-gray-900 truncate mt-0.5">#' + order.id + ' — ' + order.name + '</div>' +
                    '<div class="text-xs text-gray-600 mt-0.5">Tutar: <strong class="text-emerald-700">' + order.total_amount + '</strong></div>' +
                    '<a href="' + order.url + '" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#C87A53] hover:underline mt-2">' +
                        'Siparişi İncele <i class="fa-solid fa-arrow-right text-[9px]"></i>' +
                    '</a>' +
                '</div>' +
                '<button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 p-1">' +
                    '<i class="fa-solid fa-times"></i>' +
                '</button>';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 50);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-4');
                setTimeout(() => toast.remove(), 400);
            }, 8000);
        }

        async function fetchRecentOrders() {
            try {
                const response = await fetch('/yonetim/api/son-siparisler?last_order_id=' + lastKnownOrderId, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;
                const data = await response.json();
                
                if (data.status === 'success') {
                    const badge = document.getElementById('orderNotificationBadge');
                    const countLabel = document.getElementById('notificationDropdownCount');
                    const list = document.getElementById('orderNotificationsList');
                    
                    if (data.count > 0) {
                        badge.innerText = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                        countLabel.innerText = data.count + ' Yeni';
                    } else {
                        badge.classList.add('hidden');
                        countLabel.innerText = '0 Yeni';
                    }

                    // Eğer yeni bir sipariş geldiyse ses çal ve toast göster
                    if (!isFirstFetch && data.has_newer && data.orders && data.orders.length > 0) {
                        playNotificationChime();
                        showLiveOrderToast(data.orders[0]);
                    }

                    if (data.latest_id) {
                        lastKnownOrderId = data.latest_id;
                    }

                    // Dropdown içeriğini doldur
                    if (data.orders && data.orders.length > 0) {
                        list.innerHTML = data.orders.map(function(order) {
                            var iconBg = order.is_new ? 'bg-amber-100 text-[#C87A53]' : 'bg-gray-100 text-gray-500';
                            return '<a href="' + order.url + '" class="p-3 hover:bg-amber-50/50 flex items-center justify-between gap-3 transition group block">' +
                                '<div class="flex items-center gap-2.5 min-w-0">' +
                                    '<div class="w-8 h-8 rounded-lg ' + iconBg + ' flex items-center justify-center shrink-0">' +
                                        '<i class="fa-solid fa-shopping-bag text-xs"></i>' +
                                    '</div>' +
                                    '<div class="min-w-0">' +
                                        '<div class="font-bold text-gray-900 truncate group-hover:text-[#C87A53] text-xs">' +
                                            '#' + order.id + ' — ' + order.name +
                                        '</div>' +
                                        '<div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1.5">' +
                                            '<span>' + order.time_ago + '</span>' +
                                            '<span>•</span>' +
                                            '<span class="font-semibold text-emerald-600">' + order.status + '</span>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="font-extrabold text-[#C87A53] text-xs whitespace-nowrap">' +
                                    order.total_amount +
                                '</div>' +
                            '</a>';
                        }).join('');
                    } else {
                        list.innerHTML = 
                            '<div class="p-6 text-center text-gray-400 text-xs">' +
                                '<i class="fa-solid fa-inbox text-2xl text-gray-300 mb-2 block"></i>' +
                                'Henüz bekleyen yeni sipariş bulunmuyor.' +
                            '</div>';
                    }

                    isFirstFetch = false;
                }
            } catch (err) {
                console.warn('Notification fetch error:', err);
            }
        }

        function toggleOrderNotifications() {
            const menu = document.getElementById('orderNotificationMenu');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                fetchRecentOrders();
            }
        }

        // Dropdown dışına tıklandığında kapat
        document.addEventListener('click', function(e) {
            const container = document.getElementById('orderNotificationDropdownContainer');
            const menu = document.getElementById('orderNotificationMenu');
            if (container && menu && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Sayfa yüklendiğinde ve her 30 saniyede bir kontrol et
        fetchRecentOrders();
        setInterval(fetchRecentOrders, 30000);
    </script>

    @stack('scripts')
</body>
</html>
