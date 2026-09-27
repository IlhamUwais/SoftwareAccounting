<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // NPWP pembeli (16 digit, tanpa strip) – digunakan untuk memvalidasi
            // bahwa FPM PDF yang di-upload memang milik customer yang sedang aktif.
            $table->string('npwp', 16)->nullable()->after('nama_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('npwp');
        });
    }
};
