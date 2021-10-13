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
        return $this->csvReader->readCSVGenerator($fileName);
    }

    /**
     * @param Contact $data
     */
    public function writeLn($data): string
    {
        return $data[0];
    }

    /**
     * @param array $data
     */
    public function handle($data)
    {
        if ('EntityNumber' === $data[0]) {
            return;
        }
        if (!$contact = $this->contactRepository->checkExist(
            $data[1],
            $data[0],
            $data[2]
        )) {
            $contact = new Contact();
            $contact->entityContact = $data[1];
            $contact->entityNumber = $data[0];
            $contact->contactType = $data[2];
        }
        $this->updateContact($contact, $data);
    }

    /**
     * "EntityNumber","EntityContact","ContactType","Value".
     */
    private function updateContact(Contact $contact, array $data)
    {
        $contact->value = $data[3];
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
