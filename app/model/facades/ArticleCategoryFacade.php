<?php

namespace App\Model\Facades;

use App\Model\Entities\ArticleCategory;
use Nette\InvalidArgumentException;
use App\Model\Entities\User;
use App\Model\Queries\ArticleCategoriesListQuery;

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
    
    public function getCategoriesCount() 
    {
        return (int)$this->em->createQuery("
                    SELECT COUNT (ac.id)
                    FROM App\Model\Entities\ArticleCategory ac
                    ")->getSingleScalarResult();
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
    
    /**
     * Vrati ID a nazvy kategorii (pro formularovy select)
     * @return ResultSet ID a nazvy kategorii
     */
    public function getIdsAndNames() 
    {
        return $this->em->getRepository(ArticleCategory::class)->findPairs([], "name", [], "id");
    }
    
    /**
     * Vrati kategorie s poctem svych clanku 
     * @return ResultSet kategorie s poctem clanku
     */
    public function getArticlesCountInCategories() 
    {
        $query = new ArticleCategoriesListQuery();
        $query->addArticlesCount();
        return $this->em->getRepository(ArticleCategory::class)->fetch($query);
    }

    /**
     * Editace kategorie clanku
     * @param ArticleCategory $category kategorie clanku
     * @param User $user                uzivatel
     * @param $data                     menene informace o kategorii
     * @throws InvalidArgumentException Uzivatel nema pravo editovat kategorii
     */
    public function editCategory(ArticleCategory $category, User $user, $data)
    {
        if (!$user->isAdmin())
            throw new InvalidArgumentException("youHaveNoPermissionsToEditCategory");

        $category->name = $data->name;
        $category->description = $data->description;
        $this->em->flush();
    }

    /**
     * Vymaze kategorii s danym ID
     * @param int|NULL $id              ID kategorie, ktera se ma smazat
     * @throws InvalidArgumentException Jestlize kategorie s danym ID neexistuje
     */
    public function deleteCategory($id = NULL)
    {
        $category = $this->getCategory($id);
        if (is_null($category)) throw new InvalidArgumentException("categoryDoesntExist");

        $this->em->remove($category);
        $this->em->flush();
    }
}
