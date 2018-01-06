<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use App\Model\Facades\ArticleCategoryFacade;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use App\Model\Facades\UserFacade;
use Kdyby\Translation\Translator;
use Nette\Security\User;


/**
 * Description of ArticleCategoryFormFactory
 *
 * @author Admin
 */
class ArticleCategoryFormFactory extends BaseFormFactory
{
    /** @var ArticleCategoryFacade Fasada pro praci s kategoriemi clanku */
    private $articleCategoryFacade;
    
    /**
     * Rozsireny konstruktor tridy {@link BaseFormFactory}
     * @param ArticleCategoryFacade $articleCategoryFacade injektovana fasada pro praci s kategoriemi clanku
     * @inheritdoc
     */
    public function __construct(ArticleCategoryFacade $articleCategoryFacade, UserFacade $userFacade, Translator $translator, User $user) 
    {
        parent::__construct($userFacade, $translator, $user);
        $this->articleCategoryFacade = $articleCategoryFacade;
    }
    
    /**
     * Vytvari formular pro praci s kategoriemi clanku
     * @return Form $form Formular pro praci s kategoriemi clanku
     */
    public function createCreateCategory() 
    {
        $form = new Form();
        $form->addText("name", $this->translator->translate("form.articleCategory.create.name"))
                ->setRequired($this->translator->translate("form.articleCategory.create.nameNotFilled"));
        
        $form->addTextArea("description", $this->translator->translate("form.articleCategory.create.description"))
                ->setRequired($this->translator->translate("form.articleCategory.create.descriptionNotFilled"))
                ->setAttribute("rows",3)
                ->setAttribute("cols", 40);
        
        $form->addSubmit("create", $this->translator->translate("form.articleCategory.create.create"));
        
        $form->onSuccess[] = array($this, "createCategorySucceeded");
        
        return $form;
    }
    
    public function createCategorySucceeded($form, $values) 
    {
        $user = $this->userFacade->getUser($this->user->id);
        try {
            $this->articleCategoryFacade->createCategory($user, $values);
        } catch (UniqueConstraintViolationException $e) {
            $form->addError($this->translator->translate("form.articleCategory.create.categoryWithThisNameAlreadyExists"));
        }
    }
}
