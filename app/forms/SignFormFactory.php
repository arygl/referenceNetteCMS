<?php

namespace App\Forms;

use App\Model\Entities\User as UserEntity;
use App\Model\Facades\UserFacade;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;
use Nette\Security\User;

/**
 * SignFormFactory slouzi k vyrobe registracniho formulare
 * @package App\Forms
 */
class SignFormFactory extends Object 
{
    /** @var UserFacade fasada pro praci s uzivateli */
    private $userFacade;
    
    /** @var Translator */
    private $translator;
    
    /** @var User Uzivatel */
    private $user;


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

