<?php

namespace App\Model\Facades;

use Kdyby\Doctrine\EntityManager;
use Nette\Object;

/**
 * Spolecny predek pro vsechny fasady
 * Predava pristup pro praci s entitami
 * @package App\Model\Facades
 */
abstract class BaseFacade extends Object
{
    /** @var EntityManager Entity manager pro praci s entitami */
    protected $em;
    
    /**
     * Konstruktor s injektovanoutridou pro praci s entitami
     * @param EntityManager $em injektovana trida pro praci s entitami
     */
    public function __construct(EntityManager $em) 
    {
        $this->em = $em;
    }
}
