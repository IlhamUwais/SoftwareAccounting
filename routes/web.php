<?php

use App\Http\Controllers\FileAccessController;
use App\Livewire\Auth\Login;
use App\Livewire\Customers\CustomerList;
use App\Livewire\Dashboard\Index as Dashboard;
use App\Livewire\MasterItems\MasterItemList;
use App\Livewire\Purchases\PurchaseForm;
use App\Livewire\Purchases\PurchaseList;
use App\Livewire\Purchases\UploadFpm;
use App\Livewire\Sales\SalesEntryList;
use App\Livewire\Spt\SptList;
use App\Livewire\Suppliers\SupplierList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'ensure.customer.active'])->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/purchases', PurchaseList::class)->name('purchases.index');
    Route::get('/purchases/upload', UploadFpm::class)->name('purchases.upload');
    Route::get('/purchases/{purchase}/edit', PurchaseForm::class)->name('purchases.edit');

    Route::get('/suppliers', SupplierList::class)->name('suppliers.index');
    Route::get('/master-items', MasterItemList::class)->name('master-items.index');
    Route::get('/sales', SalesEntryList::class)->name('sales.index');
    Route::get('/spt', SptList::class)->name('spt.index');

    // File downloads - always authorization-checked in the controller.
    Route::get('/files/purchase-documents/{document}', [FileAccessController::class, 'purchaseDocument'])->name('files.purchase-document');
    Route::get('/files/spt/{spt}', [FileAccessController::class, 'spt'])->name('files.spt');
    Route::get('/files/spt-proofs/{proof}', [FileAccessController::class, 'sptProof'])->name('files.spt-proof');

    // SuperAdmin only
    Route::middleware('role:SUPERADMIN')->group(function () {
        Route::get('/customers', CustomerList::class)->name('customers.index');
    });
});
