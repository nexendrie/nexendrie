<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form SiteSearch
 *
 * @author Jakub Konečný
 */
final class SiteSearchFormFactory {
  public const TYPE_USERS = "users";
  public const TYPE_ARTICLES_TITLES = "articlesTitles";
  public const TYPE_ARTICLES_TEXTS = "articlesTexts";
  
  protected function getTypes(): array {
    return [
      static::TYPE_USERS => "uživatelé",
      static::TYPE_ARTICLES_TITLES => "titulky článků",
      static::TYPE_ARTICLES_TEXTS => "texty článků",
    ];
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("text", "Text:", 25, 25)
      ->addRule(Form::MIN_LENGTH, "Text musí mít alespoň 3 znaky.", 3)
      ->setRequired(true);
    $form->addSelect("type", "Typ:", $this->getTypes());
    $form->addSubmit("submit", "Hledat");
    return $form;
  }
}
?>