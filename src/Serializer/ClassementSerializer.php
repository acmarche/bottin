<?php


namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Entity\Classement;

class ClassementSerializer
{
    public function __construct()
    {
    }

    public function serializeClassementForApi(Classement $classement): array
    {
        $category = $classement->getCategory();
        $parentId = $category->getParent() !== null ? $category->getParent()->getId() : 0;
        $data = [];
        $data['id'] = $category->getId();
        $data['name'] = $category->getName();
        $data['lvl'] = $category->getNodeLevel() - 1;
        $data['lft'] = '';
        $data['rgt'] = '';
        $data['root'] = preg_replace("#/#", "", $category->getRootMaterializedPath());
        $data['description'] = $category->getDescription();
        $data['logo'] = $category->getLogo();
        $data['slugname'] = $category->getSlug();
        $data['slug'] = $category->getSlug();
        $data['parent'] = $parentId;

        return $data;
    }

    public function serializeClassementForApiAndroid(Classement $classement): array
    {
        $data = [];
        $data['id'] = $classement->getId();
        $data['fiche_id'] = $classement->getFiche()->getId();
        $data['category_id'] = $classement->getCategory()->getId();
        $data['principal'] = (bool)$classement->getPrincipal();

        return $data;
    }
}
