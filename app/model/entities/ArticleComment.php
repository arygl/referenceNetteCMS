<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Class ArticleComment Entita pro tabulku komentaru k clankum
 * @package App\Model\Entities
 * @Entity
 * @Table(name="article_comment")
 */
class ArticleComment extends BaseEntity
{
    /**
     * Sloupec pro ID komentare
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * Sloupec pro obsah komentare
     * @Column(type="text")
     */
    protected $content;

    /**
     * Sloupec pro datum pridani komentare
     * @Column(type="datetime")
     */
    protected $date;

    /**
     * Vazba N:1 komentare na clanek
     * @ManyToOne(targetEntity="Article", inversedBy="comments")
     * @JoinColumn(name="article_id", referencedColumnName="id")
     */
    protected $article;

    /**
     * Vazba N:1 komentare na uzivatele
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $author;
}