<?php

namespace App;

use Nette;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Router tovarna
 * Ridi routovani v cele aplikaci
 * @package App
 */
class RouterFactory
{
	/**
         * Vytvari routu pro aplikaci
	 * @return IRouter
	 */
	public static function createRouter()
	{
            $router = new RouteList;
            $router[] = new Route("[<locale=cs cs|en>/]<presenter>/<action>[/<id>]", "Homepage:default"); //nepovinny parametr locale, defaultne nastavena cestia
            return $router;
	}
}
