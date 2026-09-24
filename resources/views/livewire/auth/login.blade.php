<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-sm bg-white shadow rounded-lg p-8">
        <h1 class="text-xl font-semibold mb-6 text-center">Masuk</h1>

        <form wire:submit="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" wire:model="username" autofocus
                       class="mt-1 w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                @error('username') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" wire:model="password"
                       class="mt-1 w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 text-white rounded py-2 font-medium hover:bg-indigo-700">
                Masuk
            </button>
        </form>
    </div>
</div>
