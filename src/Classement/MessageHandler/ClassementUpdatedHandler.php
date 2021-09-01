<?php

namespace AcMarche\Bottin\Classement\MessageHandler;

use AcMarche\Bottin\Classement\Message\ClassementUpdated;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClassementUpdatedHandler implements MessageHandlerInterface
{
    public function __invoke(ClassementUpdated $classementUpdated): void
    {
    }
}
