<?php

namespace App\Model\Entities;

use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Entita pro user tabulku
 * @package App\Model\Entities
 * @Entity
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
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    
    /**
     * username column
     * @Column(type="string")
     */
    protected $name;
    
    /**
     * password column
     * @Column(type="string")
     */
    protected $password;
    
    /**
     * email column
     * @Column(type="string")
     */
    protected $email;   
    
    /**
     * ip addres column
     * @Column(type="string")
     */
    protected $ip;  
    
    /**
     * registration date column
     * @Column(name="`registration_date`",type="datetime")
     */
    protected $registrationDate;  
    
    /**
     * user role column
     * @Column(type="integer")
     */
    protected $role;  
    
    /**
     * Namapovana vazba uzivatele 1:N na clanky
     * @OneToMany(targetEntity="Article", mappedBy="user")
     */
    protected $articles; // atribut musi byt objektem tridy ArrayCollection

    /**
     * Namapovana vazba mezi uzivatelem a nastavenim uctu 1:1
     * @OneToOne(targetEntity="UserSettings", mappedBy="user")
     */
    protected $settings;
    
    /**
     * Konstruktor s inicializaci objektu pro vazby mezi entitami
     */
    public function __construct() 
    {
        $this->articles = new ArrayCollection();
    }
    
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
        $article->user = $this;
    }
    
    /**
     * zjistuje, zda je uzivatel admin
     * @return bool vrai TRUE, pokud je uzivatel admin, jinak FALSE
     */
    public function isAdmin() 
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
