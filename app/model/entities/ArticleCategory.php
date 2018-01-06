<?php

namespace App\Model\Entities;

use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

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
}
