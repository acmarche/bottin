<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Address;
use AcMarche\Bottin\Bce\Repository\AddressRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class AddressHandler implements ImportHandlerInterface
{
    private AddressRepository $addresseRepository;
    private CsvReader $csvReader;

    public function __construct(AddressRepository $addresseRepository, CsvReader $csvReader)
    {
        $this->addresseRepository = $addresseRepository;
        $this->csvReader = $csvReader;
    }

    /**
     * @return Address[]
     *
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }


    /**
     * @param Address $data
     */
    public function handle($data)
    {
        if ($address = $this->addresseRepository->checkExist($data->entityNumber, $data->zipcode)) {
            $address->box = $data->box;
            $address->typeOfAddress = $data->typeOfAddress;
            $address->extraAddressInfo = $data->extraAddressInfo;
            $address->houseNumber = $data->houseNumber;
            $address->countryFR = $data->countryFR;
            $address->countryNL = $data->countryNL;
            $address->municipalityFR = $data->municipalityFR;
            $address->municipalityNL = $data->municipalityNL;
            $address->streetFR = $data->streetFR;
            $address->streetNL = $data->streetNL;
            $address->dateStrikingOff = $data->dateStrikingOff;
        } else {
            $address = $data;
            $this->addresseRepository->persist($address);
        }
    }

    /**
     * @param Address $data
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
        return 'address';
    }
}
