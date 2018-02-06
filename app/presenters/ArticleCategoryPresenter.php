<?php

namespace App\Presenters;

use App\Forms\ArticleCategoryFormFactory;
use App\Model\Facades\ArticleCategoryFacade;
use Nette\Application\UI\Form;

/**
 * Presenter kategorii clanku
 * @package App\Presenters
 */
class ArticleCategoryPresenter extends BasePresenter
{
    /** @var ArticleCategoryFacade Fasada pro manipulaci s kategoriemi clanku */
    private $articleCategoryFacade;
    
    /** @var ArticleCategoryFormFactory Tovarnicka na vyrobu formulare pro pridavani kategorii */
    private $formFactory;
    
    /**
     * Konstruktor s injektovanou fasadou pro manipulaci s kategoriemi clanku a tovarnickou na vyrobu formulare pro pridavani kategorii
     * @param ArticleCategoryFacade $articleCategoryFacade  Fasada pro manipulaci s kategoriemi clanku
     * @param ArticleCategoryFormFactory $formFactory       Tovarnicka na vyrobu formulare pro pridavani kategorii
     */
    public function __construct(ArticleCategoryFacade $articleCategoryFacade, ArticleCategoryFormFactory $formFactory) 
    {
        parent::__construct();
        $this->articleCategoryFacade = $articleCategoryFacade;
        $this->formFactory = $formFactory;
    }
    
    /**
     * Pokud uzivatel nema opravneni spravovat kategorie clanku, bude presmerovan na homepage
     */
    public function actionManage() 
    {
        if (!$this->userEntity->isAdmin()) $this->redirect ("Homepage:default");
    }
    
    /**
     * Vytvari a vraci komponentu formulare pro vytvareni kategorii
     * @return Form Komponenta formulare pro vytvareni kategorii
     */
    public function createComponentCreateCategoryForm() 
    {
        $form = $this->formFactory->createCreateCategory();
        $form->onSuccess[] = function (Form $form)
        {
            $pres = $form->getPresenter();
            $pres->flashMessage($this->translator->translate("articleCategory.categoryWasCreated"));
            $pres->redirect("this");
        };
        
        return $form;
    }
    
    /** Predava sablone info o poctu clanku v kategoriich */
    public function renderManage() 
    {
        $this->template->categories = $this->articleCategoryFacade->getArticlesCountInCategories();
    }
}
