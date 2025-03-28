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
        Schema::table('tbl_location', function (Blueprint $table) {
            //
            $table->tinyInteger("status")->default(1);
            $table->string("slug_name");
            $table->string("meta_title")->nullable;
            $table->string("meta_keyword")->nullable;
            $table->string("meta_description")->nullable;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_location', function (Blueprint $table) {
            //
        });
    }
};
