<?php

namespace App\Model\Facades;

use App\Model\Entities\Article;
use App\Model\Entities\ArticleCategory;
use App\Model\Entities\User;
use Nette\InvalidArgumentException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

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

    /**
     * Edituje dany clanek
     * @param Article $article  Clanek k editaci
     * @param $data             Editovana data
     */
    public function editArticle(Article $article, $data)
    {
        $article->title = $data->title;
        $article->category = $data->category;
        $article->content = $data->content;
        $this->em->flush();
    }

    /**
     * Oznaci clanek s danym ID jako schvaleny
     * @param int $id                   ID clanku ke schvaleni
     * @param User $user                Uzivatel
     * @throws InvalidArgumentException Jeslize uzivatel nema opravneni nebo clanek s danym ID neexistuje
     */
    public function releaseArticle($id, User $user)
    {
        if (!$user->isAdmin()) throw new InvalidArgumentException("youHaveNoPermissionsToReleaseArticles");

        $article = $this->getArticle($id);
        if (is_null($article)) throw new InvalidArgumentException("articleDoesntExist");

        $article->released = TRUE;
        $article->releaseDate = new DateTime();
        $this->em->flush();
    }
}
