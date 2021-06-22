<?php

namespace AcMarche\Bottin\Adresse\MessageHandler;

use AcMarche\Bottin\Adresse\Message\AdresseDeleted;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AdresseDeletedHandler implements MessageHandlerInterface
{
    public function __invoke(AdresseDeleted $adresseDeleted)
    {
    }
}
