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
        Schema::table('shop_situation', function (Blueprint $table) {
            $table->foreign(['shop_id'])->references(['id'])->on('shops')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['situation_id'])->references(['id'])->on('situations')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_situation', function (Blueprint $table) {
            $table->dropForeign('shop_situation_shop_id_foreign');
            $table->dropForeign('shop_situation_situation_id_foreign');
        });
    }
};
