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
        Schema::create('property_booking_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('booking_request_id')->length('255')->nullable();
            $table->integer('property_booking_id');
            $table->string('name')->length('255')->nullable();
            $table->string('email')->length('255')->nullable();
            $table->double('amount', 10, 2)->nullable();
            $table->enum('payment_mode', ['Payu', 'Cash', 'NEFT'])->nullable();
            $table->enum('booking_request_status', ['pending', 'paid', 'declined'])->nullable();
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
        Schema::dropIfExists('property_booking_payment_requests');
    }
};
