<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="h-full bg-navy-50 text-navy-900"
      x-data="sidebarState()"
      x-init="init()"
      @keydown.escape="closeMobile()">

    {{-- ═══════════════════════════════════════════════════════
         SIDEBAR
    ════════════════════════════════════════════════════════ --}}

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-navy-950/60 z-30 lg:hidden"
         x-show="mobileOpen"
         x-cloak
         @click="closeMobile()"
         style="display:none"></div>

    {{-- Sidebar panel --}}
    <aside id="sidebar"
           class="fixed top-0 left-0 h-full z-40 flex flex-col bg-navy-900 shadow-sidebar"
           :class="{
               'w-70': !collapsed || mobileOpen,
               'w-[4.5rem]': collapsed && !mobileOpen,
               '-translate-x-full lg:translate-x-0': !mobileOpen && collapsed && isSmall,
               'translate-x-0': mobileOpen || !isSmall
           }"
           x-cloak>

        {{-- Brand / logo area --}}
        <div class="flex items-center gap-3 px-4 h-16 border-b border-navy-800 flex-shrink-0">
            {{-- Icon mark --}}
            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gold-500 flex items-center justify-center">
                 <button class="hidden lg:flex ml-auto w-8 h-8 items-center justify-center rounded-md text-navy-400 hover:text-white transition-colors flex-shrink-0"
                    @click="toggleDesktop()"
                    :title="collapsed ? 'Buka sidebar' : 'Minimalkan sidebar'">
                <svg x-show= "!collapsed" class="w-5 h-5 text-navy-900" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6zm1 2h6v1.5H7V6zm0 3h6v1.5H7V9zm0 3h4v1.5H7V12z" clip-rule="evenodd"/>
                </svg>
                <svg x-show= "collapsed" class="w-5 h-5 text-navy-900" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6zm1 2h6v1.5H7V6zm0 3h6v1.5H7V9zm0 3h4v1.5H7V12z" clip-rule="evenodd"/>
                </svg>
                </button>
            </div>
            {{-- Brand name --}}
            <span class="sidebar-logo-text nav-label font-bold text-white text-base whitespace-nowrap overflow-hidden"
                  :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">
                Accounting System   
            </span>
            {{-- Desktop collapse toggle (icon only, hidden on mobile) --}}
            <!-- <button class="hidden lg:flex ml-auto w-7 h-7 items-center justify-center rounded-md text-navy-400 hover:text-white hover:bg-navy-700 transition-colors flex-shrink-0"
                    @click="toggleDesktop()"
                    :title="collapsed ? 'Buka sidebar' : 'Minimalkan sidebar'">
                <svg x-show="!collapsed" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <svg x-show="collapsed" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button> -->
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-3 space-y-0.5">

            @auth
                @if(auth()->user()->isSuperAdmin())
                    {{-- SuperAdmin section --}}
                    <div class="mb-3">
                        <p class="nav-label px-3 mb-1 text-[10px] font-semibold text-navy-500 uppercase tracking-widest whitespace-nowrap overflow-hidden"
                           :class="collapsed && !mobileOpen ? 'opacity-0' : 'opacity-100'">Admin</p>
                        <a href="{{ route('customers.index') }}" wire:navigate
                           class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                           :title="collapsed && !mobileOpen ? 'Daftar Customer' : ''">
                            <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Daftar Customer</span>
                        </a>
                        <a href="{{ route('audit-logs.index') }}" wire:navigate
                           class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}"
                           :title="collapsed && !mobileOpen ? 'Audit Log' : ''">
                            <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Audit Log</span>
                        </a>
                    </div>
                    <div class="my-2 mx-3 border-t border-navy-800"></div>
                @endif

                {{-- Main navigation --}}
                <p class="nav-label px-3 mb-1 mt-2 text-[10px] font-semibold text-navy-500 uppercase tracking-widest whitespace-nowrap overflow-hidden"
                   :class="collapsed && !mobileOpen ? 'opacity-0' : 'opacity-100'">Menu</p>

                <a href="{{ route('dashboard') }}" wire:navigate
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   :title="collapsed && !mobileOpen ? 'Dashboard' : ''">
                    <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Dashboard</span>
                </a>

                <a href="{{ route('purchases.index') }}" wire:navigate
                   class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}"
                   :title="collapsed && !mobileOpen ? 'Pembelian' : ''">
                    <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Pembelian</span>
                </a>

                <a href="{{ route('suppliers.index') }}" wire:navigate
                   class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                   :title="collapsed && !mobileOpen ? 'Supplier' : ''">
                    <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Supplier</span>
                </a>

                <a href="{{ route('master-items.index') }}" wire:navigate
                   class="nav-link {{ request()->routeIs('master-items.*') ? 'active' : '' }}"
                   :title="collapsed && !mobileOpen ? 'Master Barang' : ''">
                    <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Master Barang</span>
                </a>

                <a href="{{ route('sales.index') }}" wire:navigate
                   class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"
                   :title="collapsed && !mobileOpen ? 'Penjualan' : ''">
                    <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Penjualan</span>
                </a>

                <a href="{{ route('spt.index') }}" wire:navigate
                   class="nav-link {{ request()->routeIs('spt.*') ? 'active' : '' }}"
                   :title="collapsed && !mobileOpen ? 'SPT' : ''">
                    <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap overflow-hidden" :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">SPT</span>
                </a>
            @endauth
        </nav>

        {{-- User section --}}
        @auth
        <div class="flex-shrink-0 border-t border-navy-800 px-3 py-3">
            <a href="{{ route('profile') }}" wire:navigate
               class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-navy-700 transition-colors duration-200 group"
               :title="collapsed && !mobileOpen ? '{{ auth()->user()->username }}' : ''">
                {{-- Avatar --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gold-500 flex items-center justify-center">
                    <span class="text-navy-900 text-xs font-bold uppercase">{{ substr(auth()->user()->username, 0, 1) }}</span>
                </div>
                {{-- Name + company --}}
                <div class="nav-label overflow-hidden whitespace-nowrap flex-1 min-w-0"
                     :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->username }}</p>
                    @if(auth()->user()->customer)
                        <p class="text-xs text-navy-400 truncate">{{ auth()->user()->customer->nama_perusahaan }}</p>
                    @endif
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit"
                        class="nav-link w-full text-left text-navy-400 hover:text-red-400"
                        :title="collapsed && !mobileOpen ? 'Keluar' : ''">
                    <svg class="nav-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap overflow-hidden"
                          :class="collapsed && !mobileOpen ? 'opacity-0 w-0' : 'opacity-100'">Keluar</span>
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- ═══════════════════════════════════════════════════════
         MAIN AREA
    ════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col min-h-screen transition-[margin] duration-250"
         :class="{
             'lg:ml-64': !collapsed,
             'lg:ml-[4.5rem]': collapsed
         }">

        {{-- Top bar --}}
        <header class="sticky top-0 z-20 h-16 bg-white border-b border-navy-100 shadow-card flex items-center px-4 gap-3">
            {{-- Hamburger (mobile) --}}
            <button class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg text-navy-500 hover:bg-navy-50 hover:text-navy-900 transition-colors"
                    @click="openMobile()" aria-label="Buka menu">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title slot (filled by JS from <h1> or static) --}}
            <div class="flex-1 truncate">
                <span class="text-sm font-semibold text-navy-700 hidden sm:block">{{ config('app.name') }}</span>
            </div>

            {{-- Right side: profile chip --}}
            @auth
            <a href="{{ route('profile') }}" wire:navigate
               class="flex items-center gap-2 rounded-lg px-3 py-1.5 hover:bg-navy-50 transition-colors">
                <div class="w-7 h-7 rounded-full bg-gold-500 flex items-center justify-center flex-shrink-0">
                    <span class="text-navy-900 text-[11px] font-bold uppercase">{{ substr(auth()->user()->username, 0, 1) }}</span>
                </div>
                <span class="text-sm font-medium text-navy-700 hidden sm:block">{{ auth()->user()->username }}</span>
            </a>
            @endauth
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6">
            {{ $slot }}
        </main>
    </div>

    {{-- Alpine.js sidebar state --}}
    <script>
        function sidebarState() {
            return {
                collapsed: false,
                mobileOpen: false,
                isSmall: false,

                init() {
                    // Restore desktop collapse state from localStorage
                    const saved = localStorage.getItem('sidebar_collapsed');
                    this.collapsed = saved === 'true';
                    this.isSmall = window.innerWidth < 1024;

                    window.addEventListener('resize', () => {
                        this.isSmall = window.innerWidth < 1024;
                        if (!this.isSmall) this.mobileOpen = false;
                    });
                },

                toggleDesktop() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('sidebar_collapsed', this.collapsed);
                },

                openMobile() {
                    this.mobileOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeMobile() {
                    this.mobileOpen = false;
                    document.body.style.overflow = '';
                },
            }
        }
    </script>

    @livewireScripts
</body>
</html>
