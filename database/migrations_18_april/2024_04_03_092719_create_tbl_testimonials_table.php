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
        Schema::create('tbl_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->string('file_type');
            $table->text('headline');
            $table->string('guest_name');
            $table->string('home_name');
            $table->string('date');
            $table->tinyInteger('position')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('add_ip', 20);
            $table->string('add_by', 50);
            $table->string('update_ip', 20)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_testimonials');
    }
};
