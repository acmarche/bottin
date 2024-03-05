<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Classement;

class ClassementSerializer
{
    public function __construct()
    {
    }

    public function serializeClassementForApi(Classement $classement): array
    {
        $category = $classement->category;
        $parentId = $category->parent instanceof Category ? $category->parent->getId() : 0;
        $data = [];
        $data['id'] = $category->getId();
        $data['name'] = $category->name;
        $data['lvl'] = $category->getNodeLevel() - 1;
        $data['lft'] = '';
        $data['rgt'] = '';
        $data['root'] = preg_replace('#/#', '', $category->getRootMaterializedPath());
        $data['description'] = $category->description;
        $data['logo'] = $category->logo;
        $data['icon'] = $category->icon;
        $data['slugname'] = $category->getSlug();
        $data['slug'] = $category->getSlug();
        $data['parent'] = $parentId;

        return $data;
    }

    public function serializeClassementForApiAndroid(Classement $classement): array
    {
        return ['id' => $classement->getId(), 'fiche_id' => $classement->fiche->getId(), 'category_id' => $classement->category->getId(), 'principal' => (bool) $classement->principal];
    }
}
