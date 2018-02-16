<?php

namespace App\Model\Queries;

use App\Model\Entities\User;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use Doctrine\ORM\QueryBuilder;

/**
 * Class UserListQuery  Trida pro skladani DQL dotazu nad uzivateli
 * @package App\Model\Queries
 */
class UserListQuery extends QueryObject
{
    /** @var  array Pole filtru aplikovanych na dotaz */
    private $filters = array();

    /**
     * Sklada DQL dotaz nad uzivateli
     * @param Queryable $repository Databazovy repozitar
     * @return QueryBuilder         Objekt pro sestavovani DQL dotazu s prednastavenym dotazem
     */
    public function doCreateQuery(Queryable $repository)
    {
        $queryBuilder = $repository->createQueryBuilder()
                ->select("u")
                ->from(User::class,"u");

        foreach ($this->filters as $filter)
            $filter($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Seradi uzivatele podle jmena vzestupne ci sestupne
     * @param string $order Poradi
     * @return $this
     */
    public function orderByName($order = "ASC")
    {
        if ($order !== "ASC" && $order !== "DESC") $order = "ASC";

        $this->filters[] = function (QueryBuilder $queryBuilder) use ($order)
        {
            $queryBuilder->addOrderBy("u.name", $order);
        };
        return $this;
    }
}