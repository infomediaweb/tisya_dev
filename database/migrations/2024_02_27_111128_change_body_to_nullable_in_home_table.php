<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_homes', function (Blueprint $table) {
            $table->integer('no_of_staff')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_homes', function (Blueprint $table) {
            $table->integer('no_of_staff')->nullable(false)->change();
        });
    }
};
