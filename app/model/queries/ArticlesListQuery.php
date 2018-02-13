<?php

namespace App\Model\Queries;

use App\Model\Entities\Article;
use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 * Class ArticlesListQuery  Pomocna trida pro skladani DQL dotazu nad clanky
 * @package App\Model\Queries
 */
class ArticlesListQuery extends QueryObject
{
    /** @var  array Pole filtru aplikovanych na dotaz */
    private $filters;

    /**
     * Sklada DQL dotazy na clanky
     * @param Queryable $repository Databazovy repozitar
     * @return QueryBuilder         Objekt pro sestavovani DQL dotazu s prednastavenym dotazem
     * @inheritdoc
     */
    public function doCreateQuery(Queryable $repository)
    {
        $queryBuilder = $repository->createQueryBuilder()
            ->select("a")
            ->from(Article::class,"a")
            ->addOrderBy("a.releaseDate","DESC");

        foreach ($this->filters as $filter)
            $filter($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Vyfiltruje pouze schvalene clanky
     * @return $this    pro moznost aplikovat dalsi operace
     */
    public function onlyReleased()
    {
        $this->filters[] = function (QueryBuilder $queryBuilder)
        {
            $queryBuilder->andWhere("a.released = 1");
        };

        return $this;
    }

    /**
     * Vyfiltruje pouze NEschvalene clanky
     * @return $this    pro moznost aplikovat dalsi operace
     */
    public function onlyNoReleased()
    {
        $this->filters[] = function (QueryBuilder $queryBuilder)
        {
            $queryBuilder->andWhere("a.released = 0");
        };

        return $this;
    }
}