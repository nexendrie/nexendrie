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
  /** @var \Nexendrie\Model\Group */
  protected $model;
  /** @var \Nexendrie\Orm\Group */
  protected $group;

  public function __construct(\Nexendrie\Model\Group $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->model->user = $user;
  }

  public function create(\Nexendrie\Orm\Group $group): Form {
    $this->group = $group;
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
      ->addRule(Form::INTEGER, "Úroveň skupiny musí být číslo.")
      ->addRule(Form::MAX_LENGTH, "Úroveň skupiny může mít maximálně 5 znaků.", 5)
      ->setRequired("Zadej úroveň skupiny.");
    $form->addText("maxLoan", "Maximální půjčka:")
      ->addRule(Form::INTEGER, "Maximální půjčka musí být číslo.")
      ->addRule(Form::MAX_LENGTH, "Maximální půjčka může mít maximálně 5 znaků.", 5)
      ->setRequired("Zadej maximální půjčku.");
    $form->addSubmit("send", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    $form->setDefaults($group->toArray());
    return $form;
  }

  public function process(Form $form, array $values): void {
    $this->model->edit($this->group->id, $values);
  }
}
?>