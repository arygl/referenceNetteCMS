<?php

namespace App\Presenters;

use App\Forms\ArticleFormFactory;
use App\Model\Facades\ArticleCategoryFacade;
use App\Model\Facades\ArticleFacade;
use Nette\Application\UI\Form;
use App\Model\Entities\Article;

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

    /** @var  Article Clanek k editaci */
    private $searchedArticle;
    
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

    /** Presmeruje na domovskou stranku, pokud neni uzivatel admin */
    public function actionDetailAdmin($id)
    {
        if (!$this->userEntity->isAdmin()) $this->redirect("Homepage:default");
    }

    public function renderDetailAdmin($id)
    {
        $this->template->article = $this->articleFacade->getArticle($id);
    }
    
    /**
     * Preda sablone data o clanku, u ktereho bude videt detail
     * @param int $id ID clanku, u ktereho bude videt detail
     */
    public function renderDetail($id)
    {
        $this->template->article = $this->articleFacade->getArticle($id);
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

    /**
     * Vytvari komponentu formulare pro editaci clanku
     * @return Form Komponenta formulare pro editaci clanku
     */
    public function createComponentEditArticleForm()
    {
        $form = $this->formFactory->createEditArticle();
        $form->onSuccess[] = function () {
            $this->flashMessage($this->translator->translate("article.articleWasEdited"));
            $this->redirect("this");
        };
        return $form;
    }

    /**
     * Naplni formular pro editaci clanku daty z editovaneho clanku
     * Pokud uzivatel neni vlastnikem clanku nebo administratorem, nebo neni zadano ID, presmeruje na homepage
     * @param int|NULL $id  ID clanku k editaci
     */
    public function actionEdit($id = NULL)
    {
        if (is_null($id) || !$this->getUser()->isLoggedIn()) $this->redirect("Homepage:default");

        $this->searchedArticle = $article = $this->articleFacade->getArticle($id);

        if ($article->user !== $this->userEntity && !$this->userEntity->isAdmin()) $this->redirect("Homepage:default");

        isset($article->category) ? $category = $article->category->id : $category = NULL;

        if (isset($article))
            $this["editArticleForm"]->setDefaults(array(
                "articleId" => $id,
                "title" => $article->title,
                "category" => $category,
                "content" => $article->content
            ));
    }

    /** odesle sablone data o clanku, ktery chceme editovat */
    public function renderEdit()
    {
        $this->template->article = $this->searchedArticle;
    }

    /**
     * Obsluhuje signal ke schvaleni clanku
     * @param int $id   ID clanku ke schvaleni
     */
    public function handleReleaseArticle($id)
    {
        if (isset($id))
            try {
                $this->articleFacade->releaseArticle($id, $this->userEntity);
                $this->flashMessage($this->translator->translate("article.articleWasReleased"));
            } catch (InvalidArgumentException $e) {
                $this->flashMessage($e->getMessage());
            }
        $this->redirect("Article:detailAdmin", array("id" => $id));
    }
}
