<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Meta;
use AcMarche\Bottin\Bce\Repository\MetaRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class MetaHandler implements ImportHandlerInterface
{
    use SymfonyStyleFactory;

    private MetaRepository $metaRepository;

    public function __construct(MetaRepository $metaRepository)
    {
        $this->metaRepository = $metaRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'meta';
    }

    /**
     * @param Meta[] $metas
     */
    public function handle(iterable $metas)
    {
        foreach ($metas as $data) {
            if (!$this->metaRepository->findByVariable($data->variable)) {
                $meta = $data;
                $this->metaRepository->persist($meta);
            }
            $this->writeLn($data->variable);
        }
        $this->metaRepository->flush();
    }
}
