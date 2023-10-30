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

    public function __construct(private readonly HoraireRepository $horaireRepository)
    {
    }

    public function getAllDays(): array
    {
        return [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7];
    }

    public function initHoraires(Fiche $fiche): void
    {
        $allDays = $this->getAllDays();
        $horairesOld = $fiche->horaires;
        foreach ($horairesOld as $horaireOld) {
            $day = $horaireOld->day;
            unset($allDays[$day]);
        }

        foreach ($allDays as $day) {
            $horaire = new Horaire();
            $horaire->fiche = $fiche;
            $horaire->day=$day;
            $fiche->addHoraire($horaire);
        }

        $this->sortHoraires($fiche);
    }

    public function sortHoraires(Fiche $fiche)
    {
        $horaires = [];
        foreach ($fiche->horaires as $horaire) {
            $horaires[$horaire->day] = $horaire;
        }

        $this->horaires = new ArrayCollection();
        foreach ($horaires as $horaire) {
            $fiche->addHoraire($horaire);
        }

        return $fiche->horaires;
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
