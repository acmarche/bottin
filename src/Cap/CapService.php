<?php

namespace AcMarche\Bottin\Cap;

use AcMarche\Bottin\Entity\Fiche;

class CapService
{
    /**
     * url pour recherche via le site de marche.
     */
    public static function generateUrlCap(Fiche $fiche): string
    {
        $urlBase = 'https://cap.marche.be/commerces-et-entreprises/';
        $secteur = '';
        $classements = $fiche->classements;
        if (\count($classements) > 0) {
            $first = $classements[0];
            $secteur = $first->category->getSlug();
        }

        return $urlBase.$secteur.'/'.$fiche->getSlug();
    }
    public static function generateUrlCapFromArray(array $fiche): string
    {
        $urlBase = 'https://cap.marche.be/commerces-et-entreprises/';
        $secteur = '';
        $classements = $fiche['classements'];
        if (\count($classements) > 0) {
            $first = $classements[0];
            $secteur = $first['slug'];
        }

        return $urlBase.$secteur.'/'.$fiche['slug'];
    }
}
