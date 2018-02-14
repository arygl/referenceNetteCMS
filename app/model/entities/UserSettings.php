<?php

namespace App\Model\Entities;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToOne;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Class UserSettings   Entita pro tabulku nastaveni uctu uzivatele
 * @package App\Model\Entities
 * @Entity
 */
class UserSettings extends BaseEntity
{
    /**
     * Sloupec pro ID nastaveni uzivatele
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * Sloupec pro popis nastaveni uzivatele
     * @Column(type="text")
     */
    protected $description;

    /**
     * Namapovana vazba nastaveni uctu na uzivatele 1:1
     * @OneToOne(targetEntity="User", inversedBy="settings")
     */
    protected $user;
}