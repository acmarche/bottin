<?php

namespace AcMarche\Bottin\Adresse\MessageHandler;

use AcMarche\Bottin\Adresse\Message\AdresseDeleted;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler]
final class AdresseDeletedHandler
{
    public function __invoke(AdresseDeleted $adresseDeleted)
    {
    }
}
