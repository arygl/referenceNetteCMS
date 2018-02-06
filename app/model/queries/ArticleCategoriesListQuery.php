<?php
namespace App\Model\Queries;

use App\Model\Entities\ArticleCategory;
use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use App\Model\Entities\Article;

/**
 * Pomocna trida pro vytvareni DQL dotazu pro kategorie clanku 
 * @package App\Model\Queries
 */
class ArticleCategoriesListQuery  extends QueryObject
{
    /** @var array pole fronty funkci s dalsimi selecty  **/
    private $selects = array(); 


    /**
     * Sklada DQL dotaz pro kategorie clanku
     * @param Queryable $repository databazovy repositar
     * @return QueryBuilder objekt pro sestavovani DQL dotazu s prednastavenym dotazem
     * @inheritdoc 
     */
    public function doCreateQuery(Queryable $repository) 
    {
        $queryBuilder = $repository->createQueryBuilder()
            ->addSelect("c")
            ->from(ArticleCategory::class, "c") // "c" znamena alias - povinny
            ->addOrderBy("c.name", "ASC");
        
        foreach ($this->selects as $select) 
        {
            $select($queryBuilder);
        }
        
        return $queryBuilder;
    }
    
    /**
     * Pridava do fronty fukci dalsi vnoreny select pro pocet clanku v kategoriich
     */
    public function addArticlesCount() 
    {
        $this->selects[] = function (QueryBuilder $queryBuilder)
        {
            $count = $queryBuilder->getEntityManager()->createQueryBuilder()
                    ->select("COUNT(a.id)")
                    ->from(Article::class,"a")
                    ->andWhere("a.category = c")
                    ->andWhere("a.released = 1");
            
        $queryBuilder->addSelect("({$count}) AS articlesCount");
        };
    }
}
