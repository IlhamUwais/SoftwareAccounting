<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\Purchase;
use App\Models\SalesEntry;
use App\Models\SptDocument;
use App\Models\SptPaymentProof;
use App\Models\Supplier;
use App\Policies\CustomerPolicy;
use App\Policies\MasterItemPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SalesEntryPolicy;
use App\Policies\SptDocumentPolicy;
use App\Policies\SptPaymentProofPolicy;
use App\Policies\SupplierPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Customer::class => CustomerPolicy::class,
        Supplier::class => SupplierPolicy::class,
        MasterItem::class => MasterItemPolicy::class,
        Purchase::class => PurchasePolicy::class,
        SalesEntry::class => SalesEntryPolicy::class,
        SptDocument::class => SptDocumentPolicy::class,
        SptPaymentProof::class => SptPaymentProofPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
