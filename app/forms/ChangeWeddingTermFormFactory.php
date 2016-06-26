<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\Marriage;

/**
 * Factory for form ChangeWeddingTerm
 *
 * @author Jakub Konečný
 */
class ChangeWeddingTermFormFactory {
  /** @var \Nexendrie\Model\SettingsRepository */
  protected $sr;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var Marriage */
  private $marriage;
  
  function __construct(\Nexendrie\Model\SettingsRepository $sr, \Nexendrie\Orm\Model $orm) {
    $this->sr = $sr;
    $this->orm = $orm;
  }
  
  /**
   * @param Marriage $marriage
   * @return Form
   */
  function create(Marriage $marriage) {
    $format = explode(" ", $this->sr->settings["locale"]["dateTimeFormat"]);
    $this->marriage = $marriage;
    $default = new \DateTime();
    $default->setTimestamp($marriage->term);
    $form = new Form;
    $form->addDateTime("term", "Nový termín:", $format[0], $format[1])
      ->setRequired("Zadej datum a čas.")
      ->addRule([$form["term"], "validateDateTime"], "Neplatné datum.")
      ->setValue($default);
    $form->addSubmit("submit", "Změnit");
    $form->onValidate[] = array($this, "validate");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function validate(Form $form, array $values) {
    $term = $values["term"]->getTimestamp();
    if($term < time()) $form->addError("Datum nemůže být v minulosti.");
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values) {
    $this->marriage->term = $values["term"]->getTimestamp();
    $this->orm->marriages->persistAndFlush($this->marriage);
  }
}
?>