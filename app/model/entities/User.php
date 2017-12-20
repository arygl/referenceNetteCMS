<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita pro user tabulku
 * @package App\Model\Entities
 * @ORM\Entity
 */
class User extends BaseEntity 
{
    /** Konstanty pro rozdeleni uz. roli */
    const ROLE_USER = 1,
            ROLE_ADMIN = 2;
    
    /** Pomocne konstanty pro username */
    const MAX_USERNAME_LENGTH = 15,
            USERNAME_FORMAT = "^[a-zA-Z0-9]*$";
    
    
    /**
     * user ID column
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * username column
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * password column
     * @ORM\Column(type="string")
     */
    protected $password;
    
    /**
     * email column
     * @ORM\Column(type="string")
     */
    protected $email;   
    
    /**
     * ip addres column
     * @ORM\Column(type="string")
     */
    protected $ip;  
    
    /**
     * registration date column
     * @ORM\Column(name="`registration_date`",type="datetime")
     */
    protected $registrationDate;  
    
    /**
     * user role column
     * @ORM\Column(type="integer")
     */
    protected $role;  
    
    /**
     * zjistuje, zda je uzivatel admin
     * @return bool vrai TRUE, pokud je uzivatel admin, jinak FALSE
     */
    public function isAdmin() 
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
