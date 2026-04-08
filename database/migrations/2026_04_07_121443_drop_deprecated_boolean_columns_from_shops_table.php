<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        $columns = [
            'city_center', 'open_at_lunch', 'pmr', 'click_collect', 'ecommerce',
            'fax', 'contact_fax', 'admin_fax',
            'enabled', 'point_of_sale_id',
        ];

        $existing = array_filter($columns, fn (string $column): bool => Schema::hasColumn('shops', $column));

        if ($existing !== []) {
            Schema::table('shops', function (Blueprint $table) use ($existing): void {
                if (in_array('point_of_sale_id', $existing, true)) {
                    $table->dropForeign('shops_point_of_sale_id_foreign');
                }
                $table->dropColumn($existing);
            });
        }
    }
};
