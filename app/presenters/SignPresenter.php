<?php

namespace App\Presenters;

use App\Forms\SignFormFactory;
use Nette\Application\UI\Form;

/**
 * Presenter pro registraci uzivatelu
 * @package App\Presenters
 */
class SignPresenter extends BasePresenter
{
    /** @var SignFormFactory pro tvorbu registracniho formulare 
     *  @inject
     */
    public $formFactory;
    
    /**
     * vytvari a vraci komponentu registractniho formulare 
     * @return Form $form komponenta registractniho formulare 
     */
    protected function createComponentSignUpForm() 
    {
        $form = $this->formFactory->createSignUp();
        $form->onSuccess[] = function (Form $form) {
            $pres = $form->getPresenter();
            $pres->flashMessage($this->translator->translate("sign.youWereSignedUp"));
            $pres->redirect("this");
        };
        return $form;
    }
   
    /**
     * vytvari a vraci komponentu prihlasovaciho formulare 
     * @return Form $form komponenta prihlasovaciho formulare 
     */
    protected function createComponentSignInForm() 
    {
        $form = $this->formFactory->createSignIn();
        $form->onSuccess[] = function (Form $form) {
            $pres = $form->getPresenter();
            $pres->flashMessage($this->translator->translate("sign.youWereLoggedIn"));
            $pres->redirect("this");
        };   
        return $form;
    }
    
    /**
     * Presmeruje prihlaseneho uzivatele na domovskou stranku
     */
    public function actionIn() 
    {
        if ($this->getUser()->isLoggedIn()) $this->redirect("Homepage:default");
    }
}
