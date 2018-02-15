<?php

namespace App\Presenters;

use App\Forms\UserFormFactory;
use Nette\Forms\Form;

/**
 * Class UserPresenter  Presenter pro uzivatele
 * @package App\Presenters
 */
class UserPresenter extends BasePresenter
{
    /**
     * @var UserFormFactory Factory pro vyrobu formularu uzivatele
     */
    private $formFactory;

    public function __construct(UserFormFactory $formFactory)
    {
        parent::__construct();
        $this->formFactory = $formFactory;
    }

    /**
     * Naplni formular daty nastaveni uzivatele, ktere chceme upravit. Pokud neni uzivatel prihlasen,
     * presmerujeme ho na homepage
     */
    public function actionSettings()
    {
        if (!$this->user->isLoggedIn()) $this->redirect("Homepage:default");

        $settings=$this->userEntity->settings;
        $this["updateSettingsForm"]->setDefaults(array("description" => $settings->description));
    }

    /**
     * Vytvari komponentu formulare pro upravu uzivatelskeho nastaveni
     * @return Form Komponenta formulare pro upravu uzivatelskeho nastaveni
     */
    public function createComponentUpdateSettingsForm()
    {
        $form = $this->formFactory->createUpdateSettings();
        $form->onSuccess[] = function () {
            $this->flashMessage($this->translator->translate("user.settingsWereUpdated"));
            $this->redirect("this");
        };
        return $form;
    }

    /**
     * Preda sablone data o uzivateli, u ktereho chceme videt profil
     * @param $id   ID uzivatele, u ktereho chceme videt profil
     */
    public function renderProfile($id)
    {
        $this->template->searchedUser = $this->userFacade->getUser($id);
    }
}