<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Contact;
use AcMarche\Bottin\Bce\Repository\ContactRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class ContactHandler implements ImportHandlerInterface
{
    use SymfonyStyleFactory;

    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'contact';
    }

    /**
     * @param iterable|Contact[] $contacts
     */
    public function handle(iterable $contacts)
    {
        foreach ($contacts as $data) {
            if (!$this->contactRepository->checkExist($data->entityContact, $data->entityNumber, $data->contactType)) {
                $contact = $data;
                $this->contactRepository->persist($contact);
            }
            $this->writeLn($data->entityNumber);
        }
        $this->contactRepository->flush();
    }
}
