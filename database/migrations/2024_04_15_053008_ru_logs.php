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
        Schema::create('ru_logs', function (Blueprint $table) {
            $table->id();
            $table->text('api_request')->nullable();
            $table->string('response_id')->nullable();
            $table->string('api_status')->nullable();
            $table->string('message')->nullable();
            $table->text('log')->nullable();
            $table->string('add_ip')->length('800')->nullable();
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
        Schema::dropIfExists('ru_logs');
    }
};
