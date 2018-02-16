<?php

namespace App\Forms;


use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use App\Model\Entities\User as UserEntity;

/**
 * Class UserFormFactory    Factory pro vyrobu formularu pro uzivatele
 * @package App\Forms
 */
class UserFormFactory extends BaseFormFactory
{
    /**
     * Vytvari formular pro upravu nastaveni uzivatele
     * @return Form Formular pro upravu nastaveni uzivatele
     */
    public function createUpdateSettings()
    {
        $form = new Form();
        $form->addTextArea("description",$this->translator->translate("form.user.settings.description"))
                ->setAttribute("rows", 4)
                ->setAttribute("cols", 40);

        $form->addSubmit("updateSettings", $this->translator->translate("form.user.settings.update"));
        $form->onSuccess[] = array($this, "updateSettingsSucceeded");

        return $form;
    }

    /**
     * Vykona se pri uspesnem odeslani formulare pro upravu nastaveni uzivatele a pokusi se updatovat hodnoty
     * @param Form $form        Formular pro upravu nastaveni uzivatele
     * @param ArrayHash $values Hodnoty z formulare
     */
    public function updateSettingsSucceeded(Form $form, $values)
    {
        $user = $this->userFacade->getUser($this->user->getId());
        $this->userFacade->updateSettings($user, $values);
    }

    /**
     * Vytvari komponentu formulare pro pridani noveho uzivatele
     * @return Form formular pro pridani noveho uzivatele
     */
    public function createAddUser()
    {
        $form = new Form();
        $form->addText("name", $this->translator->translate("form.user.add.name"))
            ->setRequired($this->translator->translate("form.user.add.nameNotFilled"))
            ->addRule(Form::MAX_LENGTH, $this->translator->translate("form.user.add.nameMayHaveMaxLetters"), UserEntity::MAX_USERNAME_LENGTH)
            ->addRule(Form::PATTERN, $this->translator->translate("common.userNameBadFormat"), UserEntity::USERNAME_FORMAT);;

        $form->addPassword("password", $this->translator->translate("form.user.add.password"))
            ->setRequired($this->translator->translate("form.user.add.passwordNotFilled"));

        $form->addText("email", $this->translator->translate("form.user.add.email"))
            ->setRequired($this->translator->translate("form.user.add.emailNotFilled"))
            ->addRule(Form::EMAIL, $this->translator->translate("form.user.add.emailBadFormat"));

        $form->addCheckbox("isAdmin", $this->translator->translate("form.user.add.admin"));

        $form->addSubmit("addUser", $this->translator->translate("form.user.add.add"));
        $form->onSuccess[] = array($this, "addUserSucceeded");

        return $form;
    }

    /**
     * Vykona se pri uspesnem odeslani formulare a pokusi se pridat noveho uzivatele
     * @param Form $form        formular pro pridani noveho uzivatele
     * @param ArrayHash $values hodnoty z formulare
     */
    public function addUserSucceeded(Form $form, $values)
    {
        try
        {
            $this->userFacade->addUser($values);
        } catch (UniqueConstraintViolationException $e)
        {
            $form->addError($this->translator->translate("form.user.add.userWithThisNameAlreadyExists"));
        }
    }

    /**
     * Vytvari komponentu formulare pro editaci uzivatele
     * @return Form Formular pro editaci uzivatele
     */
    public function createEditUser()
    {
        $form = new Form();
        $form->addHidden("userId");

        $form->addText("name", $this->translator->translate("form.user.edit.name"))
            ->setRequired($this->translator->translate("form.user.edit.nameNotFilled"))
            ->addRule(Form::MAX_LENGTH, $this->translator->translate("form.user.edit.nameMayHaveMaxLetters"), UserEntity::MAX_USERNAME_LENGTH)
            ->addRule(Form::PATTERN, $this->translator->translate("common.userNameBadFormat"), UserEntity::USERNAME_FORMAT);;

        $form->addText("email", $this->translator->translate("form.user.edit.email"))
            ->setRequired($this->translator->translate("form.user.edit.emailNotFilled"))
            ->addRule(Form::EMAIL, $this->translator->translate("form.user.edit.emailBadFormat"));

        $form->addCheckbox("isAdmin", $this->translator->translate("form.user.edit.admin"));

        $form->addSubmit("editUser", $this->translator->translate("form.user.edit.edit"));
        $form->onSuccess[] = array($this, "editUserSucceeded");

        return $form;
    }

    /**
     * Vykona se po uspesnem odeslani formulare a pokusi se editovat data
     * @param Form $form        Formular pro editaci uzivatele
     * @param ArrayHash $values Hodnoty z formulare
     */
    public function editUserSucceeded(Form $form, $values)
    {
        try {
            $user = $this->userFacade->getUser($values->userId);
            if (is_null($user)) throw new InvalidArgumentException("userDoesntExist");

            $this->userFacade->editUser($user, $values);
        } catch (InvalidArgumentException $e) {
            $form->addError($this->translator->translate("exception.{$e->getMessage()}"));
        }
    }
}