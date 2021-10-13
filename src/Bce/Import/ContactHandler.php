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

    public function start(): void
    {
        $this->contactRepository->reset();
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
        $this->updateContact($data);
    }

    /**
     * "EntityNumber","EntityContact","ContactType","Value".
     */
    private function updateContact(array $data)
    {
        $contact = new Contact();
        $contact->entityContact = $data[1];
        $contact->entityNumber = $data[0];
        $contact->contactType = $data[2];
        $contact->value = $data[3];
        $this->contactRepository->persist($contact);
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
