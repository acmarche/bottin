<?php

namespace AcMarche\Bottin\Bce\Import;

interface ImportHandlerInterface
{
    public function readFile(string $fileName): iterable;

    public function handle($data);

    public static function getDefaultIndexName(): string;

    public function flush(): void;

    public function writeLn($data): string;

    public function start(): void;
}
