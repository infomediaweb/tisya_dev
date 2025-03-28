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
        Schema::create('tbl_home_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->integer('position')->default(0);
            $table->string('add_ip', 50)->nullable();
            $table->string('add_by')->nullable();
            $table->string('update_ip', 50)->nullable();
            $table->string('update_by')->nullable();
            $table->string('url_key', 260)->nullable();
            $table->string('meta_title', 100)->nullable();
            $table->string('meta_keyword', 350)->nullable();
            $table->string('meta_description', 350)->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_home_types');
    }
};
