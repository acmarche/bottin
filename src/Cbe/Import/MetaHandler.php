<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Meta;
use AcMarche\Bottin\Cbe\Repository\MetaRepository;

class MetaHandler implements ImportHandlerInterface
{
    private MetaRepository $metaRepository;

    public function __construct(MetaRepository $metaRepository)
    {
        $this->metaRepository = $metaRepository;
    }

  public static function getDefaultIndexName(): string
    {
        return 'hand';
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
