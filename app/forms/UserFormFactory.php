<?php

namespace App\Forms;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

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
}