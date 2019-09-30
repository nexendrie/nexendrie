<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nextras\Forms\Controls\DateTimePicker;

/**
 * Factory for form AddEditEvent
 *
 * @author Jakub Konečný
 */
final class AddEditEventFormFactory {
  /** @var \Nexendrie\Model\Events */
  protected $model;
  /** @var string */
  protected $dateTimeFormat;
  /** @var \Nexendrie\Orm\Event */
  protected $event;
  
  public function __construct(\Nexendrie\Model\Events $model, \Nexendrie\Model\SettingsRepository $sr) {
    $this->model = $model;
    $this->dateTimeFormat = $sr->settings["locale"]["dateTimeFormat"];
  }
  
  public function create(?\Nexendrie\Orm\Event $event = null): Form {
    $this->event = $event;
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.");
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $start = new DateTimePicker("Začátek:");
    $start->setRequired("Zadej začátek.");
    $form->addComponent($start, "start");
    $end = new DateTimePicker("Konec:");
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
    $form->onSuccess[] = [$this, "process"];
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