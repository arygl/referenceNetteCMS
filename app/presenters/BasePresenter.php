<?php

namespace App\Presenters;

use App\Model\Entities\User as UserEntity;
use App\Model\Facades\UserFacade;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use App\Model\Facades\ArticleFacade;

/**
 * Základní presenter pro všechny ostatni presentery v aplikaci
 * @package App/Presenters
 */
abstract class BasePresenter extends Presenter
{
    /** @persistent null|string Jazykova verze aplikace */
    public $locale;
    
    /**
     * @var UserFacade @inject Fasada pro manipulaci s uzivateli 
     */
    public $userFacade;
    
    /**
     * @var UserEntity Entita pro aktualniho uzivatele
     */
    protected $userEntity;

    /**
     * @var Translator Preklad jazyka na urovni presenteru
     * @inject
     */
    public $translator;

    /** @var  ArticleFacade Fasada pro manipulaci s clanky */
    protected $articleFacade;
    
    /**
     * Registrace makra na preklad,  diky kteremu lze v sablone prelozit text a nemusi se překlady ukladat do promennych a predavat sablonam
     * @inheritdoc
     */
    protected function createTemplate() 
    {
        /** @var Template $template Latte sablona pro aktualni presenter */
        $template = parent::createTemplate();
        $this->translator->createTemplateHelpers()
                ->register($template->getLatte());
        return $template;
    }

    public function injectArticleFacade(ArticleFacade $articleFacade)
    {
        $this->articleFacade = $articleFacade;
    }
    
    /**
     * vola se na zacatku kazde akce kazdeho presenteru a inicializuje entitu uzivatele
     */
    public function startup() 
    {
        parent::startup();
        if ($this->getUser()->isLoggedIn())
        {
            $this->userEntity = $this->userFacade->getUser($this->getUser()->getId());
        }
        else
        {
        // Aby slo pouzit $userEntity->isAdmin(), kdyz neni uzivatel prihlaset
            $entity = new UserEntity();
            $entity->role = UserEntity::ROLE_USER;
            $this->userEntity = $entity;
        }
    }
    
    /**
     * vola se pred vykreslenim sablony kazdeho presenteru a predava promenne do layoutu aplikace
     */
    public function beforeRender() 
    {
        parent::beforeRender();
        $this->template->userEntity = $this->userEntity;
        if ($this->userEntity->isAdmin())
            $this->template->newArticlesCount = $this->articleFacade->getNoReleasedArticlesCount();
    }
    
    /**
     * Odhlasi uzivatele z jakehokoliv mista (proto je v BasePresenteru)
     */
    public function handleLogout() 
    {
        $this->getUser()->logout(TRUE);
        $this->flashMessage($this->translator->translate("common.youWereLoggedOut"));
        $this->redirect("Homepage:default");
    }
}
