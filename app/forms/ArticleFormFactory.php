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
                ->setAttribute("rows", 6)
                ->setAttribute("cols", 60);

        $form->addSubmit("create", $this->translator->translate("form.article.create.create"));
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
}
