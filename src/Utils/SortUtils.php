<?php

namespace AcMarche\Bottin\Utils;

use AcMarche\Bottin\Entity\Fiche;

class SortUtils
{
    /**
     * @param Fiche[] $fiches
     *
     * @return Fiche[]
     */
    public static function sortFiche($fiches)
    {
        usort(
            $fiches,
            function ($a, $b) {
                $ad = $a->getSociete();
                $bd = $b->getSociete();
                if ($ad == $bd) {
                    return 0;
                }

                return $ad > $bd ? 1 : -1;
            }
        );

        return $fiches;
    }

    /**
     * @param \AcMarche\Bottin\Entity\Category[] $fiches
     *
     * @return \AcMarche\Bottin\Entity\Category[]
     */
    public static function sortCategories(array $categories)
    {
        usort(
            $categories,
            function ($a, $b) {
                $ad = $a->getName();
                $bd = $b->getName();
                if ($ad == $bd) {
                    return 0;
                }

                return $ad > $bd ? 1 : -1;
            }
        );

        return $categories;
    }
}
