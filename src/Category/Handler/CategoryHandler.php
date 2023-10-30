<?php

namespace AcMarche\Bottin\Category\Handler;

use AcMarche\Bottin\Entity\Category;

class CategoryHandler
{
   /**
     * @return Category[]
     */
    public static function getCategoryPath(Category $category): array
    {
        $path = [];
        while ($category) {
            if (!$category) {
                break;
            }
            $path[] = $category;
            $category = $category->parent;
        }

        return array_reverse($path);
    }
}