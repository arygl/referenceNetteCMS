<?php

namespace App\Model\Entities;

use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entita pro tabulku kategorii clanku
 * @package App\Model\Entities
 * @Entity
 */
class ArticleCategory 
{
    /**
     * Id kategorie
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    
    /**
     * Jmeno kategorie
     * @Column(type="string")
     */
    public $name;
    
    /**
     * Popis kategorie
     * @Column(type="string")
     */
    public $description;
    
    /**
     * Vazba 1:N katregorie na clanky
     * @OneToMany(targetEntity="Article", mappedBy="category")
     */
    protected $articles;
    
    /**
     * Konstruktor s inicializaci objektu pro vazby mezi entitami
     */
    public function __construct() 
    {
        parent::__construct();
        $this->articles = new ArrayCollection;
    }
    
    public function addArticle($article) 
    {
        $this->articles[] = $article;
        $article->category = $this;
    }
}
