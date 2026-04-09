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
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('address_id')->nullable()->index('shops_address_id_foreign');
            $table->string('slug')->nullable()->unique();
            $table->string('company');
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->integer('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_other')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('function')->nullable();
            $table->string('civility')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('contact_street')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('contact_postal_code')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_phone_other')->nullable();
            $table->string('contact_mobile')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('admin_function')->nullable();
            $table->string('admin_civility')->nullable();
            $table->string('admin_last_name')->nullable();
            $table->string('admin_first_name')->nullable();
            $table->string('admin_phone')->nullable();
            $table->string('admin_phone_other')->nullable();
            $table->string('admin_mobile')->nullable();
            $table->string('admin_email')->nullable();
            $table->text('comment1')->nullable();
            $table->text('comment2')->nullable();
            $table->text('comment3')->nullable();
            $table->text('note')->nullable();
            $table->integer('ftlb')->nullable();
            $table->string('user')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
