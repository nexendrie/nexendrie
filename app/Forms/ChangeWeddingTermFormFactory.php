<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\Marriage;
use Nextras\Forms\Controls\DateTimePicker;

/**
 * Factory for form ChangeWeddingTerm
 *
 * @author Jakub Konečný
 */
final class ChangeWeddingTermFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var Marriage */
  private $marriage;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  public function create(Marriage $marriage): Form {
    $this->marriage = $marriage;
    $form = new Form();
    $term = new DateTimePicker("Nový termín:");
    $term->setRequired("Zadej datum a čas.");
    $term->setValue($marriage->term);
    $form->addComponent($term, "term");
    $form->addSubmit("submit", "Změnit");
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function validate(Form $form, array $values): void {
    if($values["term"] === null) {
      return;
    }
    $term = $values["term"]->getTimestamp();
    if($term < time()) {
      $form->addError("Datum nemůže být v minulosti.");
    }
  }
  
  public function process(Form $form, array $values): void {
    $this->marriage->term = $values["term"]->getTimestamp();
    $this->orm->marriages->persistAndFlush($this->marriage);
  }
}
?>