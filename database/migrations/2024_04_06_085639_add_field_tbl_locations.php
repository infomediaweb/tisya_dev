<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::table('tbl_location', function (Blueprint $table) {
            $table->integer("ru_location_id")->nullable()->after('state_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::table('tbl_location', function (Blueprint $table) {
            $table->dropColumn('ru_location_id');
        });
    }
};
