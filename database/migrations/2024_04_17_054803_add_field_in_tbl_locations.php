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
            $table->integer("tax")->nullable()->after('location_name')->comment('In percentage');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::table('tbl_location', function (Blueprint $table) {
            $table->dropColumn('tax');
        });
    }
};
