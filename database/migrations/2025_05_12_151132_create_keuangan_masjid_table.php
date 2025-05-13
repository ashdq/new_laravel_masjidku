<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('keuangan_masjid', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
        });

        // Inisialisasi saldo awal
        DB::table('keuangan_masjid')->insert([
            'saldo' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('keuangan_masjid');
    }
};
