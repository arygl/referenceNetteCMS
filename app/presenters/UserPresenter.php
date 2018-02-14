<?php

namespace App\Presenters;

use App\Forms\UserFormFactory;

/**
 * Class UserPresenter  Presenter pro uzivatele
 * @package App\Presenters
 */
class UserPresenter extends BasePresenter
{
    /**
     * @var UserFormFactory Factory pro vyrobu formularu uzivatele
     */
    private $userFormFactory;

    public function __construct(UserFormFactory $userFormFactory)
    {
        parent::__construct();
        $this->userFormFactory = $userFormFactory;
    }
}