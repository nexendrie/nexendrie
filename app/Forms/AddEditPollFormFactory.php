<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditPoll
 *
 * @author Jakub Konečný
 */
final class AddEditPollFormFactory {
  /** @var \Nexendrie\Model\Polls */
  protected $model;
  /** @var \Nexendrie\Orm\Poll */
  protected $poll;

  public function __construct(\Nexendrie\Model\Polls $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->model->user = $user;
  }

  public function create(?\Nexendrie\Orm\Poll $poll = null): Form {
    $this->poll = $poll;
    $form = new Form();
    $form->addText("question", "Otázka:")
      ->addRule(Form::MAX_LENGTH, "Otázka může mít maximálně 60 znaků.", 60)
      ->setRequired("Zadej otázku.");
    $form->addTextArea("answers", "Odpovědi:")
      ->setRequired("Zadej alespoň jednu odpověď.")
      ->setOption("description", "Každou odpověď napiš na nový řádek.");
    $form->addCheckbox("locked", "Uzamčená");
    $form->addSubmit("send", "Odeslat");
    $form->onSuccess[] = [$this, "process"];
    if($poll !== null) {
      $form->setDefaults($poll->toArray());
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if($this->poll === null) {
      $this->model->add($values);
    } else {
      $this->model->edit($this->poll->id, $values);
    }
  }
}
?>