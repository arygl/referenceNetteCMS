<?php
namespace App\Model\Queries;

use App\Model\Entities\ArticleCategory;
use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 * Pomocna trida pro vytvareni DQL dotazu pro kategorie clanku 
 * @package App\Model\Queries
 */
class ArticleCategoriesListQuery  extends QueryObject
{
    /**
     * Sklada DQL dotaz pro kategorie clanku
     * @param Queryable $repository databazovy repositar
     * @return QueryBuilder objekt pro sestavovani DQL dotazu s prednastavenym dotazem
     * @inheritdoc 
     */
    public function doCreateQuery(Queryable $repository) 
    {
        return $repository->createQueryBuilder()
                ->addSelect("c")
                ->from(ArticleCategory::class, "c") // "c" znamena alias - povinny
                ->addOrderBy("c.name", "ASC");
    }
}
