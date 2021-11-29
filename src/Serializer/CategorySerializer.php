<?php

namespace AcMarche\Bottin\Serializer;

use AcMarche\Bottin\Bottin;
use AcMarche\Bottin\Entity\Category;
use Symfony\Component\Serializer\SerializerInterface;

class CategorySerializer
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializeCategory(Category $category): array
    {
        $std = json_decode($this->serializer->serialize($category, 'json', ['groups' => 'group1']), true, 512, JSON_THROW_ON_ERROR);

        $std['updated_at'] = $category->getUpdatedAt()->format('Y-m-d');
        $std['created_at'] = $category->getCreatedAt()->format('Y-m-d');

        return $std;
    }

    public function serializeCategory2(Category $category): array
    {
        $data = [];
        $parentId = null !== $category->getParent() ? $category->getParent()->getId() : 0;
        $data['name'] = $category->getName();
        $data['description'] = $category->getDescription();
        $data['id'] = $category->getId();
        $data['lvl'] = $category->getNodeLevel() - 1; //adaptation
        $data['lft'] = '';
        $data['rgt'] = '';
        $data['root'] = preg_replace('#/#', '', $category->getRootMaterializedPath());
        $data['slugname'] = $category->getSlug();
        $data['logo'] = Bottin::url.$category->getLogo();
        $data['logo_blanc'] = Bottin::url.$category->getLogoBlanc();
        $data['parent'] = $parentId;

        return $data;
    }

    public function serializePathCategoryForApi(Category $category): array
    {
        $parentId = null !== $category->getParent() ? $category->getParent()->getId() : 0;
        $data = [];
        $data['id'] = $category->getId();
        $data['parent_id'] = $parentId;
        $data['slugname'] = $category->getSlug();
        $data['slug'] = $category->getSlug();
        $data['name'] = $category->getName();
        $data['lvl'] = $category->getNodeLevel() - 1; //adaptation
        $data['lft'] = '';
        $data['rgt'] = '';
        $data['root'] = preg_replace('#/#', '', $category->getRootMaterializedPath());
        $data['mobile'] = '';
        $data['logo'] = $category->getLogo();
        $data['description'] = $category->getDescription();
        $data['logo_blanc'] = $category->getLogoBlanc();
        $data['created'] = $category->getCreatedAt();
        $data['updated'] = $category->getUpdatedAt();
        $data['created_at'] = $category->getCreatedAt();
        $data['updated_at'] = $category->getUpdatedAt();
        $data['createdAt'] = $category->getCreatedAt();
        $data['updatedAt'] = $category->getUpdatedAt();

        return $data;
    }
}
