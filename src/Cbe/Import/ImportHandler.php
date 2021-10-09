<?php

namespace AcMarche\Bottin\Cbe\Import;

class ImportHandler
{
    private iterable $handlers;

    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    public function run(string $fileName)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle([]);
            dump($fileName);
        }
    }
}