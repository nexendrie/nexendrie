<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\Article as ArticleEntity;

/**
 * Factory for form AddEditNews
 * 
 * @author Jakub Konečný
 */
class AddEditArticleFormFactory {
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("title", "Titulek:")
      ->addRule(Form::MAX_LENGTH, "Titulek může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titulek.");
    $form->addSelect("category", "Kategorie:", ArticleEntity::getCategories())
      ->setRequired("Vyber kategorii.");
    $form->addTextArea("text", "Text:")
      ->setRequired("Zadej text.");
    $form->addCheckbox("allowedComments", "Povolit komentáře")
      ->setValue(true);
    $form->addSubmit("send", "Odeslat");
    return $form;
  }
}
?>