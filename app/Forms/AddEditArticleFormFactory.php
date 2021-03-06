<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\Article as ArticleEntity;

/**
 * Factory for form AddEditNews
 * 
 * @author Jakub Konečný
 */
final class AddEditArticleFormFactory {
  public function create(): Form {
    $form = new Form();
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