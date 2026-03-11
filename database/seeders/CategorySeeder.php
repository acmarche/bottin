<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Alimentation' => [
                'Boulangerie-Pâtisserie' => [
                    'Pain artisanal',
                    'Viennoiseries',
                ],
                'Boucherie-Charcuterie',
                'Épicerie fine',
                'Fruits et Légumes',
                'Fromagerie',
            ],
            'Mode et Accessoires' => [
                'Vêtements Femme' => [
                    'Prêt-à-porter',
                    'Lingerie',
                ],
                'Vêtements Homme',
                'Chaussures' => [
                    'Homme',
                    'Femme' => [
                        'Sport',
                        'Ville',
                    ],
                ],
                'Bijoux et Montres',
            ],
            'Maison et Décoration' => [
                'Mobilier',
                'Décoration intérieure',
                'Jardinerie' => [
                    'Plantes',
                    'Outillage de jardin',
                ],
                'Électroménager',
            ],
            'Santé et Bien-être' => [
                'Pharmacie',
                'Optique',
                'Cosmétiques et Parfumerie' => [
                    'Soins visage',
                    'Soins corps',
                    'Parfums',
                ],
            ],
            'Services' => [
                'Banque et Assurance',
                'Coiffure et Esthétique' => [
                    'Coiffeur',
                    'Institut de beauté',
                ],
                'Immobilier',
                'Automobile' => [
                    'Garage et réparation',
                    'Location de véhicules',
                ],
            ],
            'Loisirs et Culture' => [
                'Librairie-Papeterie',
                'Sport' => [
                    'Articles de sport',
                    'Salle de sport',
                ],
                'Jeux et Jouets',
                'Musique et Instruments',
            ],
        ];

        foreach ($categories as $name => $children) {
            $root = Category::factory()->create([
                'name' => $name,
            ]);

            $this->createChildren($children, $root, "/{$root->id}/");
        }
    }

    /**
     * @param  array<int|string, mixed>  $children
     */
    private function createChildren(array $children, Category $parent, string $path): void
    {
        foreach ($children as $key => $value) {
            $name = is_string($key) ? $key : $value;

            $child = Category::factory()->create([
                'name' => $name,
                'parent_id' => $parent->id,
            ]);

            if (is_array($value)) {
                $this->createChildren($value, $child, "{$path}{$child->id}/");
            }
        }
    }
}
