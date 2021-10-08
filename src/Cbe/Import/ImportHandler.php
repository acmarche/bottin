<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Code;
use AcMarche\Bottin\Cbe\Entity\Meta;
use AcMarche\Bottin\Cbe\Repository\CodeRepository;
use AcMarche\Bottin\Cbe\Repository\MetaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class ImportHandler
{
    private ObjectManager $objectManager;
    private CodeRepository $codeRepository;
    private MetaRepository $metaRepository;

    public function __construct(EntityManagerInterface $objectManager, CodeRepository $codeRepository, MetaRepository $metaRepository)
    {
        $this->objectManager = $objectManager;
        $this->codeRepository = $codeRepository;
        $this->metaRepository = $metaRepository;
    }

    /**
     * @param array|Code[] $codes
     */
    public function handleCodes(array $codes)
    {
        foreach ($codes as $data) {
            if (!$this->codeRepository->checkExist($data->Code, $data->Language, $data->Category)) {
                $code = $data;
                $this->objectManager->persist($code);
            }
        }
        $this->codeRepository->flush();
    }

    /**
     * @param Meta[] $metas
     */
    public function handleMeta(array $metas)
    {
        foreach ($metas as $data) {
            if (!$this->metaRepository->findByVariable($data->Variable)) {
                $meta = $data;
                $this->objectManager->persist($meta);
            }
        }
        $this->metaRepository->flush();
    }
}
