<?php

namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Entity;

/**
 * Entita pro tabulku clanku 
 * @package App\Model\Entities
 * @Entity
 */
class Article extends BaseEntity
{
    /** Maximalni delka nazvu clanku */
    const MAX_TITLE_LENGTH = 25;

    /**
     * Namapovana vazba 1:N clanku na komentare
     * @OneToMany(targetEntity="ArticleComment", mappedBy="article")
     */
    protected $comments;

    /**
     * Konstruktor s inicializaci objektu pro vazby mezi entitami
     */
    public function __construct()
    {
        parent::__construct();
        $this->comments = new ArrayCollection();
    }

    /**
     * Prida komentar k danemu clanku
     * @param ArticleComment $comment   Novy komentar
     */
    public function addComment(ArticleComment $comment)
    {
        $this->comments[] = $comment;
        $comment->article = $this;
    }

    /**
     * ID clanku
     * @Id
     * @Column(type="integer")
     * @GeneratedValue 
     */
    protected $id;
    
    /**
     * Nazev clanku
     * @Column(type="string")
     */
    protected $title;
    
    /**
     * Obsah clanku
     * @Column(type="string")
     */
    protected $content;
    
    /**
     * Udaj o tom, zda byl clanek zverejnen ci ne
     * @Column(type="boolean")
     */
    protected $released;
    
    /**
     * Datum zverejneni clanku
     * @Column(type="datetime", name="released_date")
     */
    protected $releaseDate;
    
    /**
     * Vazba clanku N:1 na uzivatele
     * @ManyToOne(targetEntity="User", inversedBy="articles")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     *
     * @ManyToOne(targetEntity="ArticleCategory", inversedBy="articles")
     * @JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;
}
