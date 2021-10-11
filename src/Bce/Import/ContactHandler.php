<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Contact;
use AcMarche\Bottin\Bce\Repository\ContactRepository;

class ContactHandler implements ImportHandlerInterface
{
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
     * @param array|Contact[] $contacts
     */
    public function handle(array $contacts)
    {
        foreach ($contacts as $data) {
            if (!$this->contactRepository->checkExist($data->contact, $data->language, $data->category)) {
                $contact = $data;
                $this->contactRepository->persist($contact);
            }
        }
        $this->contactRepository->flush();
    }
}
