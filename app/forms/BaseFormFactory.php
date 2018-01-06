<?php

namespace App\Forms;

use App\Model\Facades\UserFacade;
use Kdyby\Translation\Translator;
use Nette\Object;
use Nette\Security\User;

/**
 * Zakladni Factory pro vsechny ostatni v aplikaci
 * Predava pristup ke spolecnym prvkum
 * @package App\Forms
 */
abstract class BaseFormFactory extends Object
{
    /** @var UserFacade Fasada pro praci s uzivateli */
    protected $userFacade;
    
    /** @var Translator Prekladac */
    protected $translator;
    
    /** @var User Uzivatel */
    protected $user;
    
    /**
    * Konstruktor s injektoanymi tridami
    * @param UserFacade $userFacade    injektovana trida pro praci s uzivateli
    * @param Translator $translator    injektovana trida pro preklad
    * @param User $user                injektovana trida uzivatele
    */
    public function __construct(UserFacade $userFacade, Translator $translator, User $user) 
    {
        $this->userFacade = $userFacade;
        $this->translator = $translator;
        $this->user = $user;
    }
}
