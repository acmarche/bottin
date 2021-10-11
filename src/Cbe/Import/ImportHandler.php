<?php

namespace AcMarche\Bottin\Cbe\Import;

use Symfony\Component\DependencyInjection\ServiceLocator;

class ImportHandler
{
    /**
     * @var iterable|ImportHandlerInterface[]
     */
    private iterable $handlers;
    private ServiceLocator $serviceLocator;

    public function __construct(iterable $handlers, ServiceLocator $serviceLocator)
    {
        $this->handlers = $handlers;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @throws \Exception
     */
    public function loadInterfaceByKey(string $key): ImportHandlerInterface
    {
        if ($this->serviceLocator->get($key)) {
            return $this->serviceLocator->get($key);
        }
        throw new \Exception('No handler found for '.$key);
    }

    public function run(string $fileName)
    {
        foreach ($this->handlers as $handler) {
            // $handler->handle([]);
            dump($handler::getDefaultIndexName());
            dump($fileName);
        }
    }
}
