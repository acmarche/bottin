<?php

namespace AcMarche\Bottin\Bce\Import;

interface ImportHandlerInterface
{
    public function handle(iterable $objects);
    public static function getDefaultIndexName(): string;
}
