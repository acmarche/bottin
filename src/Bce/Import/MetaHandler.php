<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Meta;
use AcMarche\Bottin\Bce\Repository\MetaRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class MetaHandler implements ImportHandlerInterface
{
    private MetaRepository $metaRepository;
    private CsvReader $csvReader;

    public function __construct(MetaRepository $metaRepository, CsvReader $csvReader)
    {
        $this->metaRepository = $metaRepository;
        $this->csvReader = $csvReader;
    }

    public function start(): void
    {
    }

    /**
     * @return Meta[]
     *
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }

    /**
     * @param Meta $data
     */
    public function handle($data)
    {
        if ($meta = $this->metaRepository->findByVariable($data->variable)) {
            $meta->value = $data->value;
        } else {
            $this->metaRepository->persist($data);
        }
    }

    /**
     * @param Meta $data
     */
    public function writeLn($data): string
    {
        return $data->variable;
    }

    public function flush(): void
    {
        $this->metaRepository->flush();
    }

    public static function getDefaultIndexName(): string
    {
        return 'meta';
    }
}
