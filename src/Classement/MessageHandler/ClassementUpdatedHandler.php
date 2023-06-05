<?php

namespace AcMarche\Bottin\Classement\MessageHandler;

use AcMarche\Bottin\Classement\Message\ClassementUpdated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ClassementUpdatedHandler
{
    public function __invoke(ClassementUpdated $classementUpdated): void
    {
    }
}
