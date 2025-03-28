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
        Schema::create('tbl_homes', function (Blueprint $table) {
            $table->id();

            $table->string('home_name', 100);
            $table->string('features_heading', 100)->nullable();
            $table->integer('home_type_id');
            $table->string('home_type', 100);
            $table->integer('state_id');
            $table->string('state', 100);
            $table->integer('location_id');
            $table->string('location', 255);
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('short_direction')->nullable();
            $table->text('direction_how_to_get_there')->nullable();
            $table->text('house_rules')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('things_to_know')->nullable();
            $table->integer('max_number_of_nights')->nullable();
            $table->integer('booking_window')->nullable();
            $table->string('checkin_time', 10)->nullable();
            $table->string('checkout_time', 10)->nullable();
            $table->integer('maximum_number_of_guests')->nullable();
            $table->integer('guests_included')->nullable();
            $table->float('extra_guest_charges', 8, 2)->nullable();
            $table->integer('no_of_staff')->default(0);
            $table->integer('no_of_bedrooms')->default(0);
            $table->integer('no_of_bathrooms')->default(0);  
            
            $table->string('map_latitude', 50)->nullable();
            $table->string('map_longitude', 50)->nullable();
            $table->string('map_text', 1000)->nullable();
            $table->string('googlelocation_url', 250)->nullable();
           

            $table->boolean('pet_friendly')->default(false);
            $table->boolean('stags_allowed')->default(false);
            $table->boolean('private_pool')->default(false);
            $table->float('weekly_discounts')->nullable();
            $table->float('monthly_discounts')->nullable();

            $table->string('owner_name', 150)->nullable();
            $table->string('owner_email', 150)->nullable();
            $table->string('owner_alternate_email', 150)->nullable();
            $table->string('owner_mobile', 50)->nullable();
            $table->string('owner_pan', 100)->nullable();
            $table->string('owner_company_name', 150)->nullable();
            $table->string('owner_gst_number', 50)->nullable();
            $table->string('owner_country', 100)->nullable();
            $table->string('owner_state', 100)->nullable();
            $table->string('owner_city', 100)->nullable();
            $table->string('owner_address', 120)->nullable();
            $table->string('tourism_license_number', 50)->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->float('owner_share')->nullable();
            
            $table->boolean('status')->default(1);
            $table->integer('position')->default(0);
            $table->string('add_ip', 50);
            $table->string('add_by');
            $table->string('update_ip', 50)->nullable();
            $table->string('update_by')->nullable();
            $table->string('url_key', 260)->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_keyword', 550)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->integer('ru_property_id')->nullable();
            $table->string('ru_response_id', 200)->nullable();
            $table->string('ru_response_message', 200)->nullable();
            $table->float('pl_price')->nullable();
            $table->string('pricelab_response_message', 250)->nullable();
           
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_homes');
    }
};
