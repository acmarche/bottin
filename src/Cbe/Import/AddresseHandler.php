<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Addresse;
use AcMarche\Bottin\Cbe\Repository\AddresseRepository;

class AddresseHandler implements ImportHandlerInterface
{
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
            if (!$this->addresseRepository->checkExist($data->addresse, $data->language, $data->category)) {
                $addresse = $data;
                $this->addresseRepository->persist($addresse);
            }
        }
        $this->addresseRepository->flush();
    }
}
