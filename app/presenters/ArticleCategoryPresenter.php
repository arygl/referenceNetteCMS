<?php

namespace App\Presenters;

use App\Forms\ArticleCategoryFormFactory;
use App\Model\Entities\ArticleCategory;
use App\Model\Facades\ArticleCategoryFacade;
use Nette\Application\UI\Form;
use Nette\InvalidArgumentException;

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

    /** @var  ArticleCategory Kategorie clanku, kterou chceme editovat */
    private $editedCategory;
    
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
            $pres->redirect("ArticleCategory:manage");
        };
        
        return $form;
    }

    /**
     * Vytvari komponentu formulare pro editaci kategorii clanku
     * @return Form komponenta formulare pro editaci kategorii clanku
     */
    public function createComponentEditCategoryForm()
    {
        $form = $this->formFactory->createEditCategory();
        $form->onSuccess[] = function (Form $form)
        {
            $pres = $form->getPresenter();
            $pres->flashMessage($this->translator->translate("articleCategory.categoryWasEdited"));
            $pres->redirect("ArticleCategory:manage");
        };
        return $form;
    }

    /**
     * Naplni formular daty kategorie, kterou chceme editovat. Pokud uzivatel neni admin, presmeruje ho na homepage
     * @param NULL|int $id Id kategorie k editaci
     */
    public function actionEdit($id = NULL)
    {
        if (!$this->userEntity->isAdmin()) $this->redirect("Homepage:default");

        $this->editedCategory = $category = $this->articleCategoryFacade->getCategory($id);
        if ($category !== NULL)
            $this["editCategoryForm"]->setDefaults(array(
                "categoryId" => $id,
                "name" => $category->name,
                "description" => $category->description,
            ));
    }

    /**
     * Signal pro smazani kategorie podle ID. Kdyz neni uzivatel admin, presmeruje jej na homepage
     * @param int $id   ID kategorie, kterou chceme smazat
     */
    public function handleDeleteCategory($id)
    {
        if (!$this->userEntity->isAdmin()) $this->redirect("Homepage:default");

        try
        {
            $this->articleCategoryFacade->deleteCategory($id);
            $this->flashMessage($this->translator->translate("articleCategory.categoryWasDeleted"));
        } catch (InvalidArgumentException $e)
        {
            $this->flashMessage($this->translator->translate("exception.{$e->getMessage()}"));
        }

        $this->redirect("ArticleCategory:manage");
    }
    
    /** Predava sablone info o poctu clanku v kategoriich */
    public function renderManage() 
    {
        $this->template->categories = $this->articleCategoryFacade->getArticlesCountInCategories();
    }

    /** Preda sablone data o kategorii, kterou chceme editovat */
    public function renderEdit()
    {
        $this->template->category = $this->editedCategory;
    }

    /** Predava sablone info o poctu clanku v kategoriich */
    public function renderList()
    {
        $this->template->categories = $this->articleCategoryFacade->getArticlesCountInCategories();
    }

    /**
     * Preda sablone data o kategorii clanku, u ktere budeme videt detail
     * @param $id   ID kategorie clanku,u ktere budeme videt detail
     */
    public function renderDetail($id)
    {
        $this->template->category = $this->articleCategoryFacade->getCategory($id);
    }
}
