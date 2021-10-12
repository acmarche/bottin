<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Establishment;
use AcMarche\Bottin\Bce\Repository\EstablishmentRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class EstablishmentHandler
{
    use SymfonyStyleFactory;

    private EstablishmentRepository $establishmentRepository;

    public function __construct(EstablishmentRepository $establishmentRepository)
    {
        $this->establishmentRepository = $establishmentRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'establishment';
    }

    /**
     * @param iterable|Establishment[] $establishments
     */
    public function handle(iterable $establishments):?object
    {
        foreach ($establishments as $data) {
            if (!$this->establishmentRepository->checkExist($data->establishmentNumber)) {
                $establishment = $data;
                $this->establishmentRepository->persist($establishment);
            }
            $this->writeLn($data->establishmentNumber);
        }
        $this->establishmentRepository->flush();
    }
    public function flush(): void
    {
        // TODO: Implement flush() method.
    }
}
