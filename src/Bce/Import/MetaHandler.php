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
        if (!$meta = $this->metaRepository->findByVariable($data->variable)) {
            $this->metaRepository->persist($data);
        } else {
            $meta->value = $data->value;
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
