<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Meta;
use AcMarche\Bottin\Bce\Repository\MetaRepository;

class MetaHandler implements ImportHandlerInterface
{
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
    public function handle(array $metas)
    {
        foreach ($metas as $data) {
            if (!$this->metaRepository->findByVariable($data->variable)) {
                $meta = $data;
                $this->metaRepository->persist($meta);
            }
        }
        $this->metaRepository->flush();
    }
}
