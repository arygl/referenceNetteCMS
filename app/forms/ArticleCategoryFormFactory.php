<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use App\Model\Facades\ArticleCategoryFacade;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\InvalidArgumentException;
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
        
        $form->addSubmit("create", $this->translator->translate("form.articleCategory.create.create"))
                ->setAttribute("class", "btn btn-primary");
        
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

    /**
     * Vytvari komponentu formulare pro editaci kategorii
     * @return Form Formular pro editaci kategorii
     */
    public function createEditCategory()
    {
        $form = new Form();
        $form = new Form();
        $form->addText("name", $this->translator->translate("form.articleCategory.edit.name"))
            ->setRequired($this->translator->translate("form.articleCategory.edit.nameNotFilled"));

        $form->addTextarea("description", $this->translator->translate("form.articleCategory.create.description"))
            ->setAttribute("rows", 3)
            ->setAttribute("cols", 40)
            ->setRequired($this->translator->translate("form.articleCategory.edit.descriptionNotFilled"));

        $form->addSubmit("edit", $this->translator->translate("form.articleCategory.edit.edit"))
            ->setAttribute("class", "btn btn-primary");

        $form->addHidden("categoryId");
        $form->onSuccess[] = array($this, "editCategorySucceeded");

        return $form;
    }

    /**
     * Funkce se vykona pri uspesnem odeslani formulare pro editaci kategorii
     * @param Form $form                Formular pro editaci kategorii
     * @param ArrayHash $values         Odeslane hodnoty formulare
     * @throws InvalidArgumentException Jestlize editovana kategorie neexistuje
     */
    public function editCategorySucceeded($form, $values)
    {
        $user = $this->userFacade->getUser($this->user->id);
        try
        {
            $category = $this->articleCategoryFacade->getCategory($values->categoryId);
            if (is_null($category)) throw new InvalidArgumentException("categoryDoesntExist");
            $this->articleCategoryFacade->editCategory($category, $user, $values);
        } catch (UniqueConstraintViolationException $e)
        {
            $form->addError($this->translator->translate("form.articleCategory.create.categoryWithThisNameAlreadyExists"));
        }
    }
}
    
    
