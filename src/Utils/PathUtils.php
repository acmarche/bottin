<?php

namespace AcMarche\Bottin\Utils;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Classement;

class PathUtils
{
    /**
     * @return Category[]
     *
     * donne vetement enfant
     * premier parent => mode : indice 0
     */
    public function getPath(Category $category): array
    {
        $path = $this->getFullPath($category);
        $path[] = $category;

        return $path;
    }

    public function getFullPath(Category $category): array
    {
        $path = [];
        $parent = $category->parent;
        if ($parent instanceof Category) {
            $path[] = $parent;
            $path = array_merge(self::getFullPath($parent), $path);
        }

        return $path;
    }

    /**
     * @return Classement[]
     */
    public function setPathForClassements(array $classements): array
    {
        foreach ($classements as $classementFiche) {
            $category = $classementFiche->category;
            $path = $this->getPath($category);
            $category->path = $path;
        }

        return $classements;
    }
}
