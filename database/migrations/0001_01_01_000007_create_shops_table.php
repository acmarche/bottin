<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('point_of_sale_id')->nullable()->constrained('point_of_sales')->nullOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('slug')->nullable();
            $table->string('company');
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->integer('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_other')->nullable();
            $table->string('fax')->nullable();
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
            $table->boolean('city_center')->default(false);
            $table->boolean('open_at_lunch')->default(false);
            $table->boolean('pmr')->default(false);
            $table->boolean('click_collect')->default(false);
            $table->boolean('ecommerce')->default(false);
            $table->boolean('enabled')->default(false);
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
            $table->string('contact_fax')->nullable();
            $table->string('contact_mobile')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('admin_function')->nullable();
            $table->string('admin_civility')->nullable();
            $table->string('admin_last_name')->nullable();
            $table->string('admin_first_name')->nullable();
            $table->string('admin_phone')->nullable();
            $table->string('admin_phone_other')->nullable();
            $table->string('admin_fax')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
