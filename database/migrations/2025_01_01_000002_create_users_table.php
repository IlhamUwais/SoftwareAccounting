<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['SUPERADMIN', 'CUSTOMER']);

            // Only filled for role = CUSTOMER. One customer = one user account.
            $table->foreignId('customer_id')->nullable()
                ->constrained('customers')->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();

            $table->unique('customer_id'); // enforce 1 customer = 1 login account
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
