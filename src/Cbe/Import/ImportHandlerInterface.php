<?php

namespace AcMarche\Bottin\Cbe\Import;

interface ImportHandlerInterface
{
    public function handle(array $objects);
}