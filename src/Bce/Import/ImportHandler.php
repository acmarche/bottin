<?php

namespace AcMarche\Bottin\Bce\Import;

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
    public function loadHandlerByKey(string $key): ImportHandlerInterface
    {
        if ($this->serviceLocator->get($key)) {
            return $this->serviceLocator->get($key);
        }
        throw new \Exception('No handler found for '.$key);
    }

    public function importAll()
    {
        foreach ($this->handlers as $handler) {
            $fileName = $handler::getDefaultIndexName();
            dump($fileName);
            $i = 0;
            try {
                foreach ($handler->readFile($fileName) as $data) {
                    $handler->handle($data);
                    if (10 === $i) {
                        break;
                    }
                    ++$i;
                }
                //  $handler->flush();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }
}
