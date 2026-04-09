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
        Schema::table('category_shop', function (Blueprint $table) {
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['shop_id'])->references(['id'])->on('shops')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_shop', function (Blueprint $table) {
            $table->dropForeign('category_shop_category_id_foreign');
            $table->dropForeign('category_shop_shop_id_foreign');
        });
    }
};
