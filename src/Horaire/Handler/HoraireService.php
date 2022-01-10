<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/01/19
 * Time: 11:27.
 */

namespace AcMarche\Bottin\Horaire\Handler;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Horaire;
use AcMarche\Bottin\Repository\HoraireRepository;
use Doctrine\Common\Collections\ArrayCollection;

class HoraireService
{
    public ArrayCollection $horaires;

    public function __construct(private HoraireRepository $horaireRepository)
    {
    }

    public function getAllDays(): array
    {
        return [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7];
    }

    public function initHoraires(Fiche $fiche): void
    {
        $allDays = $this->getAllDays();
        $horairesOld = $fiche->getHoraires();
        foreach ($horairesOld as $horaireOld) {
            $day = $horaireOld->getDay();
            unset($allDays[$day]);
        }

        foreach ($allDays as $day) {
            $horaire = new Horaire();
            $horaire->setFiche($fiche);
            $horaire->setDay($day);
            $fiche->addHoraire($horaire);
        }

        $this->sortHoraires($fiche);
    }

    public function sortHoraires(Fiche $fiche)
    {
        $horaires = [];
        foreach ($fiche->getHoraires() as $horaire) {
            $horaires[$horaire->getDay()] = $horaire;
        }

        $this->horaires = new ArrayCollection();
        foreach ($horaires as $horaire) {
            $fiche->addHoraire($horaire);
        }

        return $fiche->getHoraires();
    }

    /**
     * @param Horaire[] $horaires
     */
    public function handleEdit(Fiche $fiche, iterable $horaires): void
    {
        foreach ($horaires as $horaire) {
            if ($horaire->isEmpty()) {
                $fiche->removeHoraire($horaire);
                continue;
            }

            $this->horaireRepository->persist($horaire);
        }
    }
}
