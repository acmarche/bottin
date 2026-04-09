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
        Schema::table('shop_tag', function (Blueprint $table) {
            $table->foreign(['shop_id'])->references(['id'])->on('shops')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['tag_id'])->references(['id'])->on('tags')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_tag', function (Blueprint $table) {
            $table->dropForeign('shop_tag_shop_id_foreign');
            $table->dropForeign('shop_tag_tag_id_foreign');
        });
    }
};
