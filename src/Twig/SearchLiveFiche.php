<?php

namespace AcMarche\Bottin\Twig;

use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: '@AcMarcheBottin/components/SearchLiveFiche.html.twig')]
class SearchLiveFiche
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;

    public function __construct(private readonly FicheRepository $ficheRepository)
    {
    }

    public function getFiches(): array
    {
        if (null !== $this->query) {
            return $this->ficheRepository->searchByNameAndCity($this->query, null);
        }

        return [];
    }
}
