<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Addresse;
use AcMarche\Bottin\Bce\Repository\AddresseRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class AddresseHandler implements ImportHandlerInterface
{
    private AddresseRepository $addresseRepository;
    private CsvReader $csvReader;

    public function __construct(AddresseRepository $addresseRepository, CsvReader $csvReader)
    {
        $this->addresseRepository = $addresseRepository;
        $this->csvReader = $csvReader;
    }

    /**
     * @return Addresse[]
     *
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }


    /**
     * @param Addresse $data
     */
    public function handle($data)
    {
        if ($addresse = $this->addresseRepository->checkExist($data->entityNumber, $data->zipcode)) {
            $addresse->box = $data->box;
            $addresse->typeOfAddress = $data->typeOfAddress;
            $addresse->extraAddressInfo = $data->extraAddressInfo;
            $addresse->houseNumber = $data->houseNumber;
            $addresse->countryFR = $data->countryFR;
            $addresse->countryNL = $data->countryNL;
            $addresse->municipalityFR = $data->municipalityFR;
            $addresse->municipalityNL = $data->municipalityNL;
            $addresse->streetFR = $data->streetFR;
            $addresse->streetNL = $data->streetNL;
            $addresse->dateStrikingOff = $data->dateStrikingOff;
        } else {
            $addresse = $data;
            $this->addresseRepository->persist($addresse);
        }
    }

    /**
     * @param Addresse $data
     * @return string
     */
    public function writeLn($data): string
    {
        return $data->entityNumber;
    }

    public function flush(): void
    {
        $this->addresseRepository->flush();
    }

    public static function getDefaultIndexName(): string
    {
        return 'addresse';
    }
}
