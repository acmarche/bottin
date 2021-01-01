<?php


namespace AcMarche\Bottin\Serializer;


use AcMarche\Bottin\Entity\Classement;
use Symfony\Component\Serializer\SerializerInterface;

class ClassementSerializer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializeClassementForApi(Classement $classement)
    {
        $category = $classement->getCategory();
        $parentId = $category->getParent() ? $category->getParent()->getId() : 0;
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

    public function serializeClassementForApiAndroid(Classement $classement)
    {
        $data = [];
        $data['id'] = $classement->getId();
        $data['fiche_id'] = $classement->getFiche()->getId();
        $data['category_id'] = $classement->getCategory()->getId();
        $data['principal'] = (bool)$classement->getPrincipal();

        return $data;
    }
}
