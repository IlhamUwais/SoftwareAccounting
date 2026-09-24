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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-red-600">Keluar</button>
                </form>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
