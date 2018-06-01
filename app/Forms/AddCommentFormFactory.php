<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddComment
 *
 * @author Jakub Konečný
 */
final class AddCommentFormFactory {
  public function create(): Form {
    $form = new Form();
    $form->addText("title", "Titulek:")
      ->addRule(Form::MAX_LENGTH, "Titulek může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titulek.");
    $form->addTextArea("text", "Text:")
      ->setRequired("Zadej text.");
    $form->addSubmit("send", "Odeslat");
    return $form;
  }
}
?>