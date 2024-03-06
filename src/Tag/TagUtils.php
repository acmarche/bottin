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
        $icons = [];
        foreach ($facets['tags'] as $name => $facet) {
            if ($tag = $this->tagRepository->findOneByName($name)) {
                $tag->icon = '/bottin/tags/'.$tag->icon;
                $icons[$name] = $tag;
            }
        }
        foreach ($facets['localite'] as $name => $count) {
            $icons[$name] = ['icon' => '/bundles/acmarchebottin/images/map-pin.svg', 'color' => '#EB4544'];
        }

        return $icons;
    }

    /**
     * @param string[] $tags
     * @return Tag[]
     */
    public function removePrivate(array $tags): array
    {
        $icons = [];
        foreach ($tags as $name => $count) {
            if ($tag = $this->tagRepository->findOneByName($name)) {
                if ($tag->private) {
                    continue;
                }
                $icons[$name] = $count;
            }
        }

        return $icons;
    }
}