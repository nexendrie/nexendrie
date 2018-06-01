<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form EditGroup
 *
 * @author Jakub Konečný
 */
final class EditGroupFormFactory {
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->addRule(Form::MAX_LENGTH, "Jméno skupiny může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej jméno skupiny.");
    $form->addText("singleName", "Titul člena:")
      ->addRule(Form::MAX_LENGTH, "Titul člena může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titul člena.");
    $form->addText("femaleName", "Titul členky:")
      ->addRule(Form::MAX_LENGTH, "Titul členky může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titul členky.");
    $form->addText("level", "Úroveň skpuiny:")
      ->addRule(Form::INTEGER, "Úroveň skupiny musí být číslo")
      ->addRule(Form::MAX_LENGTH, "Úroveň skupiny může mít maximálně 5 znaků.", 5)
      ->setRequired("Zadej úroveň skupiny.");
    $form->addSubmit("send", "Odeslat");
    return $form;
  }
}
?>