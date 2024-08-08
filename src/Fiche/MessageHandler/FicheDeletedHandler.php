<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Search\MeiliServer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FicheDeletedHandler
{
    public function __construct(
        private readonly MeiliServer $meiliServer
    )
    {
    }

    public function __invoke(FicheDeleted $ficheDeleted): void
    {
        try {
            $this->meiliServer->removeFiche($ficheDeleted->getFicheId());
        } catch (\Exception $e) {

        }
    }
}
