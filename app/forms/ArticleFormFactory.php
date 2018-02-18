<?php

namespace App\Forms;

use App\Model\Entities\Article;
use App\Model\Facades\ArticleCategoryFacade;
use App\Model\Facades\ArticleFacade;
use App\Model\Facades\UserFacade;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use InvalidArgumentException;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Description of ArticleFormFactory
 *
 * @author Admin
 */
class ArticleFormFactory extends BaseFormFactory
{
    /** @var ArticleFacade Fasada pro praci s clanky */
    private $articleFacade;
    
    /** @var ArticleCategoryFacade Fasada pro praci s kategoriemi clanku */
    private $articleCategoryFacade;
    
    /**
     * Rozsireny konstruktor tridy {@link BaseFormFactory} 
     * @param ArticleFacade $articleFacade                  injektovana Fasada pro praci s clanky
     * @param ArticleCategoryFacade $articleCategoryFacade  injektovanaFasada pro praci s kategoriemi clanku
     * @inheritdoc
     */
    public function __construct(ArticleFacade $articleFacade, ArticleCategoryFacade $articleCategoryFacade, UserFacade $userFacade, Translator $translator, User $user) 
    {
        parent::__construct($userFacade, $translator, $user);
        $this->articleFacade = $articleFacade;
        $this->articleCategoryFacade = $articleCategoryFacade;
    }
    
    /**
     * Vytvari a vraci komponentu formulare pro pridavani clanku
     * @return Form Formular pro pridavani clanku
     */
    public function createCreateArticle() 
    {
        $form = new Form();
        $form->addText("title", $this->translator->translate("form.article.create.title"))
                ->setRequired($this->translator->translate("form.article.create.titleNotFilled"))
                ->addRule(Form::MAX_LENGTH, $this->translator->translate("form.article.create.titleMayHaveMaxLetters"), Article::MAX_TITLE_LENGTH);
        
        $form->addSelect("category", $this->translator->translate("form.article.create.category"))
                ->setItems($this->articleCategoryFacade->getIdsAndNames())
                ->setRequired();
        
        $form->addTextarea("content", $this->translator->translate("form.article.create.content"))
                ->setRequired($this->translator->translate("form.article.create.contentNotFilled"))
                ->setAttribute("class","mceEditor");

        $form->addSubmit("create", $this->translator->translate("form.article.create.create"));
        $form->getComponent("create")->getControlPrototype()->onclick('tinyMCE.triggerSave()');
        $form->onSuccess[] = array($this, "createArticleSucceeded");

        return $form;
    }
    
    public function createArticleSucceeded($form, $values) 
    {
        $category = $this->articleCategoryFacade->getCategory($values->category);
        if (is_null($category)) throw new InvalidArgumentException("categoryDoesntExist");
        
        try {
                    $user = $this->userFacade->getUser($this->user->id);
                    $this->articleFacade->createArticle($user, $category, $values);
            } catch (UniqueConstraintViolationException $e) {
                    $form->addError($this->translator->translate("form.article.create.articleWithThisNameAlreadyExists"));
            }

    }

    /**
     * Vytvari komponentu formulare pro editaci clanku
     * @return Form Komponenta formulare pro editaci clanku
     */
    public function createEditArticle()
    {
        $form = new Form();
        $form->addHidden("articleId");
        $form->addText("title", $this->translator->translate("form.article.edit.title"))
            ->setRequired($this->translator->translate("form.article.edit.titleNotFilled"))
            ->addRule(Form::MAX_LENGTH, $this->translator->translate("form.article.edit.titleMayHaveMaxLetters"), Article::MAX_TITLE_LENGTH);

        $form->addSelect("category", $this->translator->translate("form.article.edit.category"))
            ->setItems($this->articleCategoryFacade->getIdsAndNames())
            ->setRequired();

        $form->addTextarea("content", $this->translator->translate("form.article.edit.content"))
            ->setRequired($this->translator->translate("form.article.edit.contentNotFilled"))
            ->setAttribute("class","mceEditor");

        $form->addSubmit("edit", $this->translator->translate("form.article.edit.edit"));
        $form->getComponent("edit")->getControlPrototype()->onclick('tinyMCE.triggerSave()');
        $form->onSuccess[] = array($this, "editArticleSucceeded");

        return $form;
    }

    /**
     * Vykona se pri uspesnem odeslani formulare pro editaci clanku a pokusi se ulozit editovany clanek
     * @param Form $form        formular pro editaci clanku
     * @param ArrayHash $values Odeslane hodnoty z formulare
     */
    public function editArticleSucceeded(Form $form, $values)
    {
        try
        {
            $article = $this->articleFacade->getArticle($values->articleId);
            if (is_null($article)) throw new InvalidArgumentException("articleDoesntExist");

            $category = $this->articleCategoryFacade->getCategory($values->category);
            if (is_null($category)) throw new InvalidArgumentException("articleCategoryDoesntExist");

            $values->category = $category;
            $this->articleFacade->editArticle($article,$values);
        }   catch (InvalidArgumentException $e)
        {
            $form->addError($e->getMessage());
        }
    }

    /**
     * Vytvari komponentu formulare pro pridavani komentaru
     * @return Form formular pro pridavani komentaru ke clanku
     */
    public function createAddComment()
    {
        $form = new Form();
        $form->addHidden("articleId");

        $form->addTextArea("content",$this->translator->translate("form.article.addComment.content"))
                ->setRequired($this->translator->translate("form.article.addComment.contentNotFilled"))
                ->setAttribute("rows", 6)
                ->setAttribute("cols", 60);

        $form->addSubmit("addComment",$this->translator->translate("form.article.addComment.add"));
        $form->onSuccess[] = array($this, "addCommentSucceeded");

        return $form;
    }

    /**
     * Vykona se pri uspesnem odeslani formulare k pridani komentare k clanku a pokusi se jej pridat
     * @param Form $form                Formular k pridani komentare k clanku
     * @param ArrayHash $values         Hodnoty vyplnene uzivatelem
     * @throws InvalidArgumentException Jestlize neexistuje clanek, k nemuz chceme pridat komentar
     */
    public function addCommentSucceeded(Form $form, $values)
    {
        try
        {
            $article = $this->articleFacade->getArticle($values->articleId);
            if (is_null($article)) throw new InvalidArgumentException("articleDoesntExist");

            $user = $this->user->isLoggedIn() ? $this->userFacade->getUser($this->user->getId()) : NULL;
            $this->articleFacade->addComment($article,$user,$values->content);
        }   catch (InvalidArgumentException $e)
        {
            $form->addError($e->getMessage());
        }
    }
}
