<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Presenter;


/**
 * Základní presenter pro všechny ostatni presentery v aplikaci
 * @package App/Presenters
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @persistent null|string Jazykova verze aplikace */
    public $locale;
    
    /**
     * @var \Kdyby\Translation\Translator Preklad jazyka na urovni presenteru
     * @inject
     */
    public $translator;
    
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
}
