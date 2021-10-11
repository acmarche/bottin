<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Addresse;
use AcMarche\Bottin\Bce\Repository\AddresseRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class AddresseHandler implements ImportHandlerInterface
{
    use SymfonyStyleFactory;

    private AddresseRepository $addresseRepository;

    public function __construct(AddresseRepository $addresseRepository)
    {
        $this->addresseRepository = $addresseRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'addresse';
    }

    /**
     * @param array|Addresse[] $addresses
     */
    public function handle(array $addresses)
    {
        foreach ($addresses as $data) {
            if (!$this->addresseRepository->checkExist($data->entityNumber, $data->zipcode)) {
                $addresse = $data;
                $this->addresseRepository->persist($addresse);
            }
            $this->writeLn($data->entityNumber);
        }
        $this->addresseRepository->flush();
    }
}
