<?php

namespace AcMarche\Bottin\Document\Form;

use AcMarche\Bottin\Document\Form\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('file');
    }

    public function getParent(): string
    {
        return DocumentType::class;
    }
}
