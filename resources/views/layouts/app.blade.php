<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white border-b shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <span class="font-semibold text-indigo-700">{{ config('app.name') }}</span>

                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('customers.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">Daftar Customer</a>
                        <a href="{{ route('audit-logs.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">Audit Log</a>
                    @endif
                    <a href="{{ route('dashboard') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">Dashboard</a>
                    <a href="{{ route('purchases.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">Pembelian</a>
                    <a href="{{ route('suppliers.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">Supplier</a>
                    <a href="{{ route('master-items.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">Master Barang</a>
                    <a href="{{ route('sales.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">Penjualan</a>
                    <a href="{{ route('spt.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-indigo-700">SPT</a>
                @endauth
            </div>

            @auth
                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-indigo-700 flex items-center gap-1.5">
                        <span class="bg-indigo-100 text-indigo-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold uppercase">
                            {{ substr(auth()->user()->username, 0, 1) }}
                        </span>
                        <span>{{ auth()->user()->username }}</span>
                        @if(auth()->user()->customer)
                            <span class="text-xs text-gray-400">({{ auth()->user()->customer->nama_perusahaan }})</span>
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-600">Keluar</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
