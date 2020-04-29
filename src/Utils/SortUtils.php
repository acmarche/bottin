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
}
