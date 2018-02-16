<?php

namespace App\Model\Facades;

use App\Model\Entities\User;
use App\Model\Entities\UserSettings;
use App\Model\Queries\UserListQuery;
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

    /**
     * Registruje noveho uzivatele do databaze
     * @param ArrayHash $values hodnoty pro noveho uzivatele
     */
    public function registerUser($values) 
    {
        $user = new User;
        $user->name = $values->name;
        $user->password = Passwords::hash($values->password);
        $user->email = $values->email;
        $user->ip = $values->ip;
        $user->role = User::ROLE_USER;
        $user->registrationDate = new DateTime();

        $settings = new UserSettings(); // aby se pridal zaznam i do tabulky user_settings
        $settings->user = $user;

        $this->em->persist($user);
        $this->em->persist($settings);
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

    /**
     * Zmeni nastaveni uzivatele
     * @param User $user        Uzivatel
     * @param ArrayHash $values Nove hodnoty
     */
    public function updateSettings(User $user, $values)
    {
        $settings = $user->settings;
        $settings->description=$values->description;
        $this->em->flush();
    }

    /**
     * Vraci seznam uzivatelu serazeny podle jejich jmen vzestupne
     * @return ResultSet    seznam uzivatelu
     */
    public function getUserList()
    {
        $query = new UserListQuery();
        $query->orderByName();
        return $this->em->getRepository(User::class)->fetch($query);
    }

    /**
     * Prida noveho uzivatele do databaze
     * @param ArrayHash $values hodnoty pro noveho uzivatele
     */
    public function addUser($values)
    {
        $role = $values->isAdmin ? User::ROLE_ADMIN : User::ROLE_USER;

        $user = new User();
        $user->name = $values->name;
        $user->password = Passwords::hash($values->password);
        $user->email = $values->email;
        $user->ip = "";
        $user->role = $role;
        $user->registrationDate = new DateTime();

        $settings = new UserSettings(); // aby se pridal zaznam i do tabulky user_settings
        $settings->user = $user;

        $this->em->persist($user);
        $this->em->persist($settings);
        $this->em->flush();
    }

    /**
     * Upravi hodnoty uzivatele
     * @param User $user        uzivatel
     * @param ArrayHash $values editovane udaje
     */
    public function editUser(User $user, $values)
    {
        $role = $values->isAdmin ? User::ROLE_ADMIN : User::ROLE_USER;

        $user->name = $values->name;
        $user->email = $values->email;
        $user->role = $role;

        $this->em->flush();
    }
}
