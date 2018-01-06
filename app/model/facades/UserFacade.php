<?php

namespace App\Model\Facades;

use App\Model\Entities\User;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Utils\ArrayHash;


/**
 * fasada pro manipulacemi s uzivateli
 * @package App\Model\Facades
 */
class UserFacade extends BaseFacade implements IAuthenticator
{
    /**
     * vrati entitu uzivatele podle jeho ID
     * @param integer $id ID uzivatele
     * @return User Entita uzivatele podle jeho id
     */
    public function getUser($id) 
    {
        return isset($id) ? $this->em->find(User::class, $id) : NULL;
    }
    
    
    public function registerUser($values) 
    {
        $user = new User;
        $user->name = $values->name;
        $user->password = Passwords::hash($values->password);
        $user->email = $values->email;
        $user->ip = $values->ip;
        $user->role = User::ROLE_USER;
        $user->registrationDate = new DateTime();
        $this->em->persist($user);
        $this->em->flush();
    }
    
    /**
    * Přihlásí uživatele do systému.
    * @param array $credentials jméno a heslo uživatele
    * @return Identity identitu přihlášeného uživatele pro další manipulaci
    * @throws AuthenticationException Jestliže došlo k chybě při prihlašování, např. špatné heslo nebo uživatelské jméno.
    */
    function authenticate(array $credentials) 
    {
        list($username, $password) = $credentials;
        
        $user = $this->em->getRepository(User::class)->findOneBy(array('name' => $username));
        
        if (!isset($user) || !Passwords::verify($password, $user->password)) throw new AuthenticationException("youFilledBadNameOrPassword");
        
        if (Passwords::needsRehash($user->password))
        {
            $user->password = Passwords::hash($password);
            $this->em->flush();
        }
        
        return new Identity($user->id);
    }
}
