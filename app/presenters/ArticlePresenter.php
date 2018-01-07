<?php

namespace App\Presenters;

use App\Forms\ArticleFormFactory;
use App\Model\Facades\ArticleCategoryFacade;
use App\Model\Facades\ArticleFacade;
use Nette\Application\UI\Form;

/**
 * Presnter pro clanky
 * @package App\Presenters
 */
class ArticlePresenter extends BasePresenter
{
    /** @var ArticleFacade Fasada pro praci s clanky*/
    private $articleFacade;
    
    /** @var ArticleCategoryFacade Fasada pro praci s kategoriemi clanku*/
    private $articleCategoryFacade;
    
    /** @var ArticleFormFactory Factory na vyrobu formulare pro vytvareni clanku*/
    private $formFactory;
    
    /**
     * Konstruktor s automaticky injektovanymi fasady pro praci s clanky a kategoriemi a tovarnou na vyrobu formulare pro pridavani clanku 
     * @param ArticleFacade $articleFacade                  injektovana Fasada pro praci s clanky
     * @param ArticleCategoryFacade $articleCategoryFacade  injektovana Fasada pro praci s kategoriemi clanku
     * @param ArticleFormFactory $formFactory               injektovana Factory na vyrobu formulare pro vytvareni clanku
     */
    public function __construct(ArticleFacade $articleFacade, ArticleCategoryFacade $articleCategoryFacade, ArticleFormFactory $formFactory) 
    {
        parent::__construct();
        $this->articleCategoryFacade = $articleCategoryFacade;
        $this->articleFacade = $articleFacade;
        $this->formFactory = $formFactory;
    }
    
    /**
     * Pokud uzivatel neni prihlasen, presmeruje ho na prihlasovaci stranku
     */
    public function actionCreate() 
    {
        if (!$this->getUser()->isLoggedIn()) $this->redirect ("Sign:in");
    }
    
    /**
     * Predava sablone pocet kategorii clanku
     */
    public function renderCreate() 
    {
        $this->template->articleCategoriesCount = $this->articleCategoryFacade->getCategoriesCount();
    }
    
    /**
     * Vytvari a vraci komponentu formulare pro pridavani clanku
     * @return Form $form Komponenta formulare p[ro pridavani clanku
     */
    public function createComponentCreateArticleForm() 
    {
        $form = $this->formFactory->createCreateArticle();
        $form->onSuccess[] = function (Form $form)
        {
            $pres = $form->getPresenter();
            $pres->flashMessage($this->translator->translate("article.articleWasSentToBeReleased"));
            $pres->redirect("this");
        };
        return $form;
    }
}
