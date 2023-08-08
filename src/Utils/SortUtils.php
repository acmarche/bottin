<?php

namespace AcMarche\Bottin\Utils;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;

class SortUtils
{
    /**
     * @param Fiche[] $fiches
     *
     * @return Fiche[]
     */
    public static function sortFiche($fiches): array
    {
        usort(
            $fiches,
            static function ($a, $b) {
                $ad = $a->getSociete();
                $bd = $b->getSociete();

                return $ad <=> $bd;
            }
        );

        return $fiches;
    }

    /**
     * @return Category[]
     */
    public static function sortCategories(array $categories): array
    {
        usort(
            $categories,
            static function ($a, $b) {
                $ad = $a->getName();
                $bd = $b->getName();

                return $ad <=> $bd;
            }
        );

        return $categories;
    }
}
