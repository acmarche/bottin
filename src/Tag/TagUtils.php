<?php

namespace AcMarche\Bottin\Tag;

use AcMarche\Bottin\Entity\Tag;
use AcMarche\Bottin\Tag\Repository\TagRepository;

class TagUtils
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {
    }

    /**
     * @param array $facets
     * @return Tag[]
     */
    public function getIconsFromFacet(array $facets): array
    {
        foreach ($facets['tags'] as $name => $facet) {
            if ($tag = $this->tagRepository->findOneByName($name)) {
                $icons[$name] = $tag;
            }
        }
        foreach ($facets['localite'] as $name => $count) {
            $icons[$name] = ['icon' => 'ti ti-building-church', 'color' => '#EB4544'];
        }
        //$icons['fiche'] = ['icon' => 'ti ti - cat', 'color' => '#133290'];
        return $icons;
    }
}