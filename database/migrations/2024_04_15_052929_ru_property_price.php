<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('ru_property_price', function (Blueprint $table) {
            $table->id();
            $table->integer('ru_property_id');
            $table->string('price')->nullable();
            $table->string('extra_price')->nullable();
            $table->date('price_date');
            $table->tinyinteger('status')->default(1);
            $table->softDeletes();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('ru_property_price');
    }
};
