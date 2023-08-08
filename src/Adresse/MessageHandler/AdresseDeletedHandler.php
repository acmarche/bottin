<?php

namespace AcMarche\Bottin\Adresse\MessageHandler;

use AcMarche\Bottin\Adresse\Message\AdresseDeleted;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AdresseDeletedHandler
{
    public function __invoke(AdresseDeleted $adresseDeleted)
    {
    }
}
