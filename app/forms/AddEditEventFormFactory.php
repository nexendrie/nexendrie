<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nette\Utils\DateTime;

/**
 * Factory for form AddEditEvent
 *
 * @author Jakub Konečný
 */
class AddEditEventFormFactory {
  /** @var \Nexendrie\Model\SettingsRepository */
  protected $sr;
  
  function __construct(\Nexendrie\Model\SettingsRepository $sr) {
    $this->sr = $sr;
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
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
      ->addRule(Form::RANGE, NULL, array(0, 999));
    $form->addText("workBonus", "Bonus k práci:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, NULL, array(0, 999));
    $form->addText("prayerLifeBonus", "Bonus k modlení:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, NULL, array(0, 999));
    $form->addText("trainingDiscount", "Sleva na trénink:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, NULL, array(0, 100));
    $form->addText("repairingDiscount", "Sleva na opravy:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, NULL, array(0, 100));
    $form->addText("shoppingDiscount", "Sleva na nákupy:")
      ->setRequired()
      ->addRule(Form::INTEGER)
      ->addRule(Form::RANGE, NULL, array(0, 100));
    $form->addSubmit("submit", "Odeslat");
    $form->onValidate[] = array($this, "validate");
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   */
  function validate(Form $form, array $values) {
    $format = $this->sr->settings["locale"]["dateTimeFormat"];
    $start = DateTime::createFromFormat($format, $values["start"]);
    if(!$start) $form->addError("Neplatný čas začátku.");
    $end = DateTime::createFromFormat($format, $values["end"]);
    if(!$end) $form->addError("Neplatný čas konce.");
    if($end->getTimestamp() < $start->getTimestamp()) $form->addError("Akce nemůže skončit před svým začátkem.");
  }
}
?>