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
        Schema::create('tbl_blogs', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('title');
            $table->text('description');
            $table->string('date');
            $table->string('facebook_url')->nullable();
            $table->string('linkedin_url')->nullable();
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
        Schema::dropIfExists('tbl_blogs');
    }
};
