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
        Schema::create('tbl_sitesettings', function (Blueprint $table) {
            $table->id();

            $table->string('company_name', 250);
            $table->string('company_prefix')->nullable();
            $table->string('domain_name', 500);
            $table->string('smtp', 200);
            $table->string('auth_email', 200);
            $table->string('auth_email_username', 200);
            $table->string('auth_email_password', 200);
            $table->string('info_email', 200)->nullable();
            $table->string('cc_email', 200)->nullable();
            $table->string('bcc_email', 200)->nullable();
            $table->string('url_rewrite', 10)->default('1');
            $table->string('default_meta_title', 100)->nullable();
            $table->string('default_meta_keyword', 350)->nullable();
            $table->string('default_meta_description', 200)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->float('website_markup')->default(0);

            $table->timestamps();
            $table->softDeletes(); // Soft delete column     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sitesettings');
    }
};
