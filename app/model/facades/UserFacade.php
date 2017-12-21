<?php

namespace App\Model\Facades;

use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

/**
 * fasada pro manipulacemi s uzivateli
 * @package App\Model\Facades
 */
class UserFacade extends Object
{
    /** @var EntityManager Entity Manager pro praci s entitami */
    private $em;
    
    /**
     * Automaticky injektovana trida pro praci s entitami
     * @param EntityManager $em Entity Manager pro praci s entitami
     */
    public function __construct(EntityManager $em) 
    {
        $this->em = $em;
    }
    
    
    public function getUser($id) 
    {
        return isset($id) ? $this->em->find(User::class, $id) : NULL;
    }
}
