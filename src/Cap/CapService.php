<?php


namespace AcMarche\Bottin\Cap;

use AcMarche\Bottin\Entity\Fiche;

class CapService
{
    /**
     * url pour recherche via le site de marche
     * @param Fiche $fiche
     * @return string
     */
    public static function generateUrlCap(Fiche $fiche): string
    {
        $urlBase = "https://cap.marche.be/commerces-et-entreprises/";
        $secteur = '';
        $classements = $fiche->getClassements();
        if (count($classements) > 0) {
            $first = $classements[0];
            $secteur = $first->getCategory()->getSlug();
        }

        return $urlBase.$secteur."/".$fiche->getSlug();
    }


}
