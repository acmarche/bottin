<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. point_of_sales ← pdv
        if ($this->legacyTableExists('pdv')) {
            DB::table('point_of_sales')->insertUsing(
                ['id', 'name'],
                DB::table('pdv')->select('id', 'intitule as name'),
            );
        }

        // 2. localities ← localite
        if ($this->legacyTableExists('localite')) {
            DB::table('localities')->insertUsing(
                ['id', 'name'],
                DB::table('localite')->select('id', 'nom as name'),
            );
        }

        // 3. addresses ← adresse
        if ($this->legacyTableExists('adresse')) {
            DB::table('addresses')->insertUsing(
                ['id', 'name', 'street', 'number', 'postal_code', 'city', 'longitude', 'latitude', 'slug', 'created_at', 'updated_at'],
                DB::table('adresse')->select('id', 'nom', 'rue', 'numero', 'cp', 'localite', 'longitude', 'latitude', 'slug', 'created_at', 'updated_at'),
            );
        }

        // 4. situations ← situation
        if ($this->legacyTableExists('situation')) {
            DB::table('situations')->insertUsing(
                ['id', 'name'],
                DB::table('situation')->select('id', 'name'),
            );
        }

        // 6. categories ← category
        if ($this->legacyTableExists('category')) {
            DB::table('categories')->insertUsing(
                ['id', 'parent_id', 'name', 'slug', 'description', 'mobile', 'logo', 'logo_white', 'color', 'icon', 'created_at', 'updated_at'],
                DB::table('category')->select('id', 'parent_id', 'name', 'slug', 'description', 'mobile', 'logo', 'logo_blanc', 'color', 'icon', 'created_at', 'updated_at'),
            );
        }

        // 7. tag_groups ← distinct tag.groupe values
        if ($this->legacyTableExists('tag')) {
            $groupes = DB::table('tag')
                ->whereNotNull('groupe')
                ->where('groupe', '!=', '')
                ->distinct()
                ->pluck('groupe');

            foreach ($groupes as $groupe) {
                DB::table('tag_groups')->insert(['name' => $groupe]);
            }

            // 8. tags ← tag (with tag_group_id lookup)
            $tags = DB::table('tag')->get();

            foreach ($tags as $tag) {
                $tagGroupId = null;
                if ($tag->groupe !== null && $tag->groupe !== '') {
                    $tagGroupId = DB::table('tag_groups')->where('name', $tag->groupe)->value('id');
                }

                DB::table('tags')->insert([
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'color' => $tag->color,
                    'icon' => $tag->icon,
                    'private' => $tag->private,
                    'tag_group_id' => $tagGroupId,
                    'description' => $tag->description,
                    'created_at' => $tag->created_at,
                    'updated_at' => $tag->updated_at,
                ]);
            }
        }

        // 9. shops ← fiche
        if ($this->legacyTableExists('fiche')) {
            DB::table('shops')->insertUsing(
                [
                    'id', 'point_of_sale_id', 'address_id', 'slug', 'company',
                    'street', 'number', 'postal_code', 'city',
                    'phone', 'phone_other', 'fax', 'mobile',
                    'website', 'email', 'facebook', 'twitter', 'instagram', 'tiktok', 'youtube', 'linkedin',
                    'longitude', 'latitude',
                    'city_center', 'open_at_lunch', 'pmr', 'click_collect', 'ecommerce', 'enabled',
                    'vat_number', 'function', 'civility', 'last_name', 'first_name',
                    'contact_street', 'contact_number', 'contact_postal_code', 'contact_city',
                    'contact_phone', 'contact_phone_other', 'contact_fax', 'contact_mobile', 'contact_email',
                    'admin_function', 'admin_civility', 'admin_last_name', 'admin_first_name',
                    'admin_phone', 'admin_phone_other', 'admin_fax', 'admin_mobile', 'admin_email',
                    'comment1', 'comment2', 'comment3', 'note', 'ftlb', 'user',
                    'created_at', 'updated_at',
                ],
                DB::table('fiche')->select(
                    'id', 'pdv_id', 'adresse_id', 'slug', 'societe',
                    'rue', 'numero', 'cp', 'localite',
                    'telephone', 'telephone_autre', 'fax', 'gsm',
                    'website', 'email', 'facebook', 'twitter', 'instagram', 'tiktok', 'youtube', 'linkedin',
                    'longitude', 'latitude',
                    'centreville', 'midi', 'pmr', 'click_collect', 'ecommerce', 'enabled',
                    'numero_tva', 'fonction', 'civilite', 'nom', 'prenom',
                    'contact_rue', 'contact_num', 'contact_cp', 'contact_localite',
                    'contact_telephone', 'contact_telephone_autre', 'contact_fax', 'contact_gsm', 'contact_email',
                    'admin_fonction', 'admin_civilite', 'admin_nom', 'admin_prenom',
                    'admin_telephone', 'admin_telephone_autre', 'admin_fax', 'admin_gsm', 'admin_email',
                    'comment1', 'comment2', 'comment3', 'note', 'ftlb', 'user',
                    'created_at', 'updated_at',
                ),
            );
        }

        // 10. category_shop ← classements
        if ($this->legacyTableExists('classements')) {
            DB::table('category_shop')->insertUsing(
                ['id', 'shop_id', 'category_id', 'principal'],
                DB::table('classements')->select('id', 'fiche_id', 'category_id', 'principal'),
            );
        }

        // 11. shop_tag ← fiche_tag
        if ($this->legacyTableExists('fiche_tag')) {
            DB::table('shop_tag')->insertUsing(
                ['shop_id', 'tag_id'],
                DB::table('fiche_tag')->select('fiche_id', 'tag_id'),
            );
        }

        // 12. shop_situation ← fiche_situation
        if ($this->legacyTableExists('fiche_situation')) {
            DB::table('shop_situation')->insertUsing(
                ['shop_id', 'situation_id'],
                DB::table('fiche_situation')->select('fiche_id', 'situation_id'),
            );
        }

        // 14. shop_images ← fiche_images
        if ($this->legacyTableExists('fiche_images')) {
            DB::table('media')->insertUsing(
                ['id', 'shop_id', 'is_main', 'file_name', 'mime_type', 'updated_at'],
                DB::table('fiche_images')->select('id', 'fiche_id', 'principale', 'image_name', 'mime', 'updated_at'),
            );
        }

        // 15. schedules ← horaire
        if ($this->legacyTableExists('horaire')) {
            DB::table('schedules')->insertUsing(
                ['id', 'shop_id', 'day', 'media_path', 'is_by_appointment', 'is_closed', 'morning_start', 'morning_end', 'noon_start', 'noon_end'],
                DB::table('horaire')->select('id', 'fiche_id', 'day', 'media_path', 'is_rdv', 'is_closed', 'morning_start', 'morning_end', 'noon_start', 'noon_end'),
            );
        }

        // 16. histories ← history
        if ($this->legacyTableExists('history')) {
            DB::table('histories')->insertUsing(
                ['id', 'shop_id', 'made_by', 'property', 'old_value', 'new_value', 'created_at', 'updated_at'],
                DB::table('history')->select('id', 'fiche_id', 'made_by', 'property', 'old_value', 'new_value', 'created_at', 'updated_at'),
            );
        }

        // 18. selections ← selection
        if ($this->legacyTableExists('selection')) {
            DB::table('selections')->insertUsing(
                ['id', 'category_id', 'user'],
                DB::table('selection')->select('id', 'category_id', 'user'),
            );
        }

        // 19. tokens ← token
        if ($this->legacyTableExists('token')) {
            DB::table('tokens')->insertUsing(
                ['id', 'shop_id', 'expire_at', 'uuid', 'password', 'created_at', 'updated_at'],
                DB::table('token')->select('id', 'fiche_id', 'expire_at', 'uuid', 'password', 'created_at', 'updated_at'),
            );
        }

        // 20. users ← user (legacy)
        if ($this->legacyTableExists('user') && Schema::hasColumn('user', 'nom')) {
            $legacyUsers = DB::table('user')->get();

            foreach ($legacyUsers as $legacyUser) {
                DB::table('users')->insert([
                    'name' => $legacyUser->nom,
                    'username' => $legacyUser->username,
                    'first_name' => $legacyUser->prenom,
                    'last_name' => $legacyUser->nom,
                    'email' => $legacyUser->email,
                    'password' => $legacyUser->password,
                ]);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function legacyTableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }
};
