<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\DateTime;

/**
 * Factory for form AddEditEvent
 *
 * @author Jakub Konečný
 */
final class AddEditEventFormFactory {
  /** @var string */
  protected $dateTimeFormat;
  
  public function __construct(\Nexendrie\Model\SettingsRepository $sr) {
    $this->dateTimeFormat = $sr->settings["locale"]["dateTimeFormat"];
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.");
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addText("start", "Začátek:")
      ->setRequired("Zadej začátek.");
    $form->addText("end", "Konec:")
      ->setRequired("Zadej konec.");
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
    $form->onValidate[] = [$this, "validate"];
    return $form;
  }
  
  public function validate(Form $form, array $values): void {
    $format = $this->dateTimeFormat;
    /** @var \Nette\Utils\DateTime $start */
    $start = DateTime::createFromFormat($format, $values["start"]);
    if($start == false) {
      $form->addError("Neplatný čas začátku.");
    }
    /** @var \Nette\Utils\DateTime $end */
    $end = DateTime::createFromFormat($format, $values["end"]);
    if($end == false) {
      $form->addError("Neplatný čas konce.");
    }
    if($end->getTimestamp() < $start->getTimestamp()) {
      $form->addError("Akce nemůže skončit před svým začátkem.");
    }
  }
}
?>