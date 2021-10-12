<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Contact;
use AcMarche\Bottin\Bce\Repository\ContactRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class ContactHandler implements ImportHandlerInterface
{
    private ContactRepository $contactRepository;
    private CsvReader $csvReader;

    public function __construct(ContactRepository $contactRepository, CsvReader $csvReader)
    {
        $this->contactRepository = $contactRepository;
        $this->csvReader = $csvReader;
    }

    /**
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }

    /**
     * @param Contact $data
     */
    public function writeLn($data): string
    {
        return $data->entityNumber;
    }

    /**
     * @param Contact $data
     */
    public function handle($data)
    {
        if ($contact = $this->contactRepository->checkExist(
            $data->entityContact,
            $data->entityNumber,
            $data->contactType
        )) {
            $contact->value = $data->value;
        } else {
            $contact = $data;
            $this->contactRepository->persist($contact);
        }
    }

    public function flush(): void
    {
        $this->contactRepository->flush();

    }

    public static function getDefaultIndexName(): string
    {
        return 'contact';
    }

}
