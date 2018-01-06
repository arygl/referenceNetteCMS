<?php

namespace App\Model\Facades;

use App\Model\Entities\ArticleCategory;
use Nette\InvalidArgumentException;
use App\Model\Entities\User;

/**
 * Fasada pro manipulaci s kategoriemi clanku
 * @package App\Model\Facades
 */
class ArticleCategoryFacade extends BaseFacade
{
    /**
     * najde a vrati kategorii podle jejiho ID
     * @param int|NULL $id ID kategorie
     * @return ArticleCategory|NULL vrati entitu kategorie nebo null, pokud nebyla nalezena
     */
    public function getCategory($id) 
    {
        return isset($id) ? $this->em->find(ArticleCategory::class, $id) : NULL;
    }
    
    public function createCategory(User $user, $data) 
    {
        if (!$user->isAdmin()) throw new InvalidArgumentException("youHaveNoPermissionsToCreateCategories");
        
        $category = new ArticleCategory;
        $category->name = $data->name;
        $category->description = $data->description;
        
        $this->em->persist($category);
        $this->em->flush();
    }
}
