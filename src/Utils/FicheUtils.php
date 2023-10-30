<?php

namespace AcMarche\Bottin\Utils;

use AcMarche\Bottin\Entity\Fiche;

class FicheUtils
{
    /**
     * @return array<int, string> $items
     */
    public function extractEmailsFromFiche(Fiche $fiche): array
    {
        $emails = [];
        if (filter_var($fiche->email, \FILTER_VALIDATE_EMAIL)) {
            $emails[] = $fiche->email;
        }

        if (filter_var($fiche->contact_email, \FILTER_VALIDATE_EMAIL)) {
            $emails[] = $fiche->contact_email;
        }

        if (filter_var($fiche->admin_email, \FILTER_VALIDATE_EMAIL)) {
            $emails[] = $fiche->admin_email;
        }

        return $emails;
    }

    /**
     * @return string[]
     */
    public function getTagsForElastic(Fiche $fiche): array
    {
        $tags = [];
        foreach ($fiche->classements as $classement) {
            $tags[] = $classement->category->name;
        }

        return $tags;
    }
}
