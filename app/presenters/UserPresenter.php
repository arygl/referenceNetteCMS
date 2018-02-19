<?php

namespace App\Presenters;

use App\Forms\UserFormFactory;
use Nette\Application\UI\Form;

/**
 * Class UserPresenter  Presenter pro uzivatele
 * @package App\Presenters
 */
class UserPresenter extends BasePresenter
{
    /** @var UserFormFactory Factory pro vyrobu formularu uzivatele */
    private $formFactory;

    /** @var UserEntity Uzivatel, ktery ma byt editovan */
    private $searchedUser;

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
            $this->flashMessage($this->translator->translate("user.settingsWereUpdated"), "alert-info");
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

    /** Presmeruje uzivatele na homepage, pokud neni admin */
    public function actionManage()
    {
        if (!$this->userEntity->isAdmin()) $this->redirect("Homepage:default");

    }

    /** preda sablone data o uzivatelich */
    public function renderManage()
    {
        $this->template->users = $this->userFacade->getUserList();
    }

    /** Presmeruje uzivatele na homepage, pokud neni admin */
    public function actionAdd()
    {
        if (!$this->userEntity->isAdmin()) $this->redirect("Homepage:default");
    }

    /**
     * Vytvari komponentu formulare pro pridani uzivatele
     * @return Form komponenta formulare pro pridani uzivatele
     */
    public function createComponentAddUserForm()
    {
        $form = $this->formFactory->createAddUser();
        $form->onSuccess[] = function () {
            $this->flashMessage($this->translator->translate("user.userWasAdded"), "alert-success");
            $this->redirect("this");
        };
        return $form;
    }

    /**
     * Naplni formular daty uzivatele, ktereho chceme editovat. Pokud neni uzivatel admin, presmerujeme ho
     * na homepage
     * @param int $id   ID uzivatele k editaci
     */
    public function actionEdit($id)
    {
        if (!$this->userEntity->isAdmin()) $this->redirect("Homepage:default");

        $this->searchedUser = $user = $this->userFacade->getUser($id);

        if (isset($user))
            $this["editUserForm"]->setDefaults(array(
                "userId" => $id,
                "name" => $user->name,
                "email" => $user->email,
                "isAdmin" => $user->isAdmin()
            ));
    }

    /** Preda sablone data o uzivateli k editaci */
    public function renderEdit()
    {
        $this->template->searchedUser = $this->searchedUser;
    }

    /**
     * Vytvari komponentu formulare pro editaci uzivatele
     * @return Form formular pro editaci uzivatele
     */
    public function createComponentEditUserForm()
    {
        $form = $this->formFactory->createEditUser();
        $form->onSuccess[] = function ()
        {
            $this->flashMessage($this->translator->translate("user.userWasEdited"), "alert-info");
            $this->redirect("this");
        };

        return $form;
    }
}