<?php

namespace AcMarche\Bottin\Bce\Import;

interface ImportHandlerInterface
{
    public function handle(array $objects);
    public static function getDefaultIndexName(): string;
}
