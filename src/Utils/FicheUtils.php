<?php


namespace AcMarche\Bottin\Utils;

use AcMarche\Bottin\Entity\Fiche;

class FicheUtils
{
    /**
     * @param Fiche $fiche
     */
    public function extractEmailsFromFiche(Fiche $fiche): array
    {
        $emails = [];
        if (filter_var($fiche->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $fiche->getEmail();
        }

        if (filter_var($fiche->getContactEmail(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $fiche->getContactEmail();
        }

        if (filter_var($fiche->getAdminEmail(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $fiche->getAdminEmail();
        }

        return $emails;
    }

    /**
     * @param Fiche $fiche
     * @return string[]
     */
    public function getTagsForElastic(Fiche $fiche): array
    {
        $tags = [];
        foreach ($fiche->getClassements() as $classement) {
            $tags[] = $classement->getCategory()->getName();
        }

        return $tags;
    }
}
