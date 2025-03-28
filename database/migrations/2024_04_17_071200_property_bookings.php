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
        Schema::create('property_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->length('255')->nullable();
            $table->integer('user_id');
            $table->integer('location_id');
            $table->integer('property_id');
            $table->double('total_amount', 10, 2)->nullable();
            $table->double('payable_amount', 10, 2)->nullable();
            $table->double('paid_amount', 10, 2)->default('0.00');
            $table->double('tax_amount', 10, 2)->nullable();
            $table->double('discount_amount', 10, 2)->nullable();
            $table->string('transcation_id')->length('800')->nullable();
            $table->text('customer_detail')->nullable();
            $table->enum('provider', ['stripe', 'razorpay', 'paypal', 'cashfee', 'payu'])->nullable();
            $table->enum('booking_status', ['pending', 'paid', 'declined'])->nullable();
            $table->enum('booking_created_by', ['admin', 'user'])->nullable();
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
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
        Schema::dropIfExists('property_bookings');
    }
};
