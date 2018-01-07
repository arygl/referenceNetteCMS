<?php

namespace App\Model\Facades;

use App\Model\Entities\Article;
use App\Model\Entities\ArticleCategory;
use App\Model\Entities\User;
use Nette\Utils\ArrayHash;

/**
 * Fasada pro praci se clanky
 * @package App\Model\Facades
 */
class ArticleFacade extends BaseFacade
{
    
    /**
     * Najde a vrati clanek podle jeho ID
     * @param int|NULL $id ID clanku
     * @return Article|NULL vrati entitu clanku podle jeho ID
     */
    public function getArticle($id)
    {
        return isset($id) ? $this->em->find(Article::class, $id) : NULL;
    }
    
    /**
     *  Metoda pro vytvoreni clanku 
     * @param User $user                        Uzivatel
     * @param ArticleCategory $category  Kategorie clanku
     * @param ArrayHash $data                   Uzivatelska data pro vyvoreni clanku           
     */
    public function createArticle(User $user, ArticleCategory $category, $data) 
    {
        $article = new Article();
        $article->title = $data->title;
        $article->content = $data->content;
        $article->released = FALSE;
        $user->addArticle($article);
        $category->addArticle($article);
        
        $this->em->persist($article);
        $this->em->flush();
    }
}
