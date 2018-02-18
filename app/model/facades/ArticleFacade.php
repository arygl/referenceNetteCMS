<?php

namespace App\Model\Facades;

use App\Model\Entities\Article;
use App\Model\Entities\ArticleCategory;
use App\Model\Entities\ArticleComment;
use App\Model\Entities\User;
use Nette\InvalidArgumentException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use App\Model\Queries\ArticlesListQuery;

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
     * @param User $user                 Uzivatel
     * @param ArticleCategory $category  Kategorie clanku
     * @param ArrayHash $data            Uzivatelska data pro vyvoreni clanku
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

    /**
     * Vrati dany pocet poslednich clanku
     * @param $num          Pocet clanku, ktery ma vratit
     * @return ResultSet    Vrati dany pocet poslednich clanku
     */
    public function getLatestArticles($num)
    {
        $query = new ArticlesListQuery();
        $query->onlyReleased();
        return $this->em->getRepository(Article::class)
                ->fetch($query)
                ->applyPaging(NULL,$num);
    }

    /**
     * Vraci seznam nepublikovanych clanku
     * @return ResultSet    Nepublikovane clanky
     */
    public function getNoReleasedArticles()
    {
        $query = new ArticlesListQuery();
        $query->onlyNoReleased();
        return $this->em->getRepository(Article::class)->fetch($query);
    }

    /**
     * Vrati pocet nepublikovanych clanku
     * @return int pocet nepublikovanych clanku
     */
    public function getNoReleasedArticlesCount()
    {
        return (int)$this->em->createQuery("
                SELECT COUNT(a.id)
                FROM App\Model\Entities\Article a
                WHERE a.released = 0
        ")->getSingleScalarResult();
    }

    /**
     * Prida novz komentar k danemu clanku
     * @param Article $article  Clanek
     * @param User|NULL $user   Uzivatel nebo NULL pokud nebzl prihlasen
     * @param string $content   Obsah komentare
     */
    public function addComment(Article $article, $user, $content)
    {
        $comment = new ArticleComment();
        $comment->content = $content;
        $comment->date = new DateTime();
        $comment->article = $article;
        $comment->author = $user;
        $this->em->persist($comment);
        $this->em->flush();
    }

    /**
     * Pokusi se smazat dany clanek podle jeho ID. Pokud neexistuje, vyhodi vyjimku
     * @param int|null $id  ID clanku, ktery se ma smazat
     */
    public function deleteArticle($id = NULL)
    {
        $article = $this->getArticle($id);
        if (is_null($article)) throw new InvalidArgumentException("articleDoesntExist");

        $this->em->remove($article);
        $this->em->flush();
    }
}
