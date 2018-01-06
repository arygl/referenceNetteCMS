<?php

namespace App\Forms;

use App\Model\Entities\User as UserEntity;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

/**
 * SignFormFactory slouzi k vyrobe registracniho formulare
 * @package App\Forms
 */
class SignFormFactory extends BaseFormFactory 
{
    /**
     * Vytvari komponentu registracniho formulare
     * @return Form registr. formular 
     */
    public function createSignUp() 
    {
        $form = new Form;
        $form->addText("name", $this->translator->translate("form.sign.up.name"))
                ->setRequired($this->translator->translate("form.sign.up.nameNotFilled"))
                ->addRule(Form::MAX_LENGTH, $this->translator->translate("form.sign.up.nameMayHaveMaxLetters"), UserEntity::MAX_USERNAME_LENGTH)
                ->addRule(Form::PATTERN, $this->translator->translate("common.userNameBadFormat"), UserEntity::USERNAME_FORMAT);
        
        $form->addPassword("password", $this->translator->translate("form.sign.up.password"))
                ->setRequired($this->translator->translate("form.sign.up.passwordNotFilled"));
        
        $form->addText("email", $this->translator->translate("form.sign.up.email"))
                ->setRequired($this->translator->translate("form.sign.up.emailNotFilled"))
                ->addRule(Form::EMAIL, $this->translator->translate("form.sign.up.emailBadFormat"));
        
        $form->addSubmit("signUp", $this->translator->translate("form.sign.up.signUp"));
    
        $form->onSuccess[] = array($this, "signUpSucceeded");
        
        return $form;
    }
    
    /**
     * vytvori komponentu formulare pro prihlasovani uzivatele
     * @return Form formular pro prihlasovani uzivatele
     */
    public function createSignIn() 
    {
        $form = new Form();
        $form->addText("name", $this->translator->translate("form.sign.in.name"))
                ->setRequired($this->translator->translate("form.sign.in.nameNotFilled"));
        
        $form->addPassword("password", $this->translator->translate("form.sign.in.password"))
                ->setRequired($this->translator->translate("form.sign.in.passwordNotFilled"));
        
        $form->addCheckbox("remember",$this->translator->translate("form.sign.in.remember"));
        
        $form->addSubmit("signIn", $this->translator->translate("form.sign.in.signIn"));
        
        $form->onSuccess[] = array($this, "signInSucceeded");
        
        return $form;
    }
    
    /**
     * Funkce se vykona pri uspesnem odeslani formulare a pokusi se registrovat noveho uzivatele
     * @param Form $form        registracni formular
     * @param ArrayHash $values hodnoty vyplnene uzivatelem
     */
    public function signUpSucceeded($form, $values) 
    {
        try {
            $values->ip = $form->getPresenter()->context->getService("httpRequest")->getRemoteAddress();
            $this->userFacade->registerUser($values);
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());  
        } catch (UniqueConstraintViolationException $e){
            $form->addError($this->translator->translate("form.sign.up.userWithThisNameAlreadyExists"));
        }
    }
    
    /**
     * Funkce se vykona pri uspesnem odeslani formulare a pokusi se zalogovat uzivatele
     * @param Form $form        formular pro prihlasovani
     * @param ArrayHash $values hodnoty vyplnene uzivatelem
     */
    public function signInSucceeded($form, $values) 
    {
        try {
            $this->user->login($values->name, $values->password);
            $values->remember ? $this->user->setExpiration("14 days", FALSE) : $this->user->setExpiration("20 minutes", TRUE);
        } catch (AuthenticationException $e) {
            $form->addError($this->translator->translate("exception.{$e->getMessage()}"));
        }
    }
}

