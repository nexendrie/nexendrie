<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Events;
use Nextras\FormComponents\Controls\DateTimeControl;

/**
 * Factory for form AddEditEvent
 *
 * @author Jakub Konečný
 */
final class AddEditEventFormFactory {
  private ?\Nexendrie\Orm\Event $event;
  
  public function __construct(private readonly Events $model) {
  }
  
  public function create(?\Nexendrie\Orm\Event $event = null): Form {
    $this->event = $event;
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.");
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $start = new DateTimeControl("Začátek:");
    $start->setRequired("Zadej začátek.");
    $form->addComponent($start, "start");
    $end = new DateTimeControl("Konec:");
    $end->setRequired("Zadej konec.");
    $end->addRule(Form::MIN, "Akce nemůže skončit před svým začátkem.", $form["start"]);
    $form->addComponent($end, "end");
    $form->addText("adventuresBonus", "Bonus k dobrodružstvím:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, null, [0, 999]);
    $form->addText("workBonus", "Bonus k práci:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, null, [0, 999]);
    $form->addText("prayerLifeBonus", "Bonus k modlení:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, null, [0, 999]);
    $form->addText("trainingDiscount", "Sleva na trénink:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, null, [0, 100]);
    $form->addText("repairingDiscount", "Sleva na opravy:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, null, [0, 100]);
    $form->addText("shoppingDiscount", "Sleva na nákupy:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, null, [0, 100]);
    $form->addSubmit("submit", "Odeslat");
    $form->onSuccess[] = $this->process(...);
    if($event !== null) {
      $form->setDefaults($event->dummyArray());
    }
    return $form;
  }

  public function process(Form $form, array $values): void {
    if($this->event === null) {
      $this->model->addEvent($values);
    } else {
      $this->model->editEvent($this->event->id, $values);
    }
  }
}
?>