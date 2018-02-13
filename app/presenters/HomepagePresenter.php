<?php

namespace App\Presenters;

/**
 * Class HomepagePresenter	Presenter pro domovskou stranku
 * @package App\Presenters
 */
class HomepagePresenter extends BasePresenter
{
    /** Pocet nejnovejsich clanku, ktere se maji na homepage objevit */
    const ARTICLE_COUNT = 5;

	/** Predava sablone data o clancich, ktere chceme zobrazit na homepage */
    public function renderDefault()
	{
		$this->template->articles = $this->articleFacade->getLatestArticles(self::ARTICLE_COUNT);
	}
}
