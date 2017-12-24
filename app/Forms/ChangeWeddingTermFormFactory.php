<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\Marriage,
    Nella\Forms\DateTime\DateTimeInput;

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
  
  public function __construct(\Nexendrie\Model\SettingsRepository $sr, \Nexendrie\Orm\Model $orm) {
    $this->sr = $sr;
    $this->orm = $orm;
  }
  
  public function create(Marriage $marriage): Form {
    $format = explode(" ", $this->sr->settings["locale"]["dateTimeFormat"]);
    $this->marriage = $marriage;
    $default = new \DateTime();
    $default->setTimestamp($marriage->term);
    $form = new Form();
    $term = new DateTimeInput($format[0], $format[1], "Nový termín:");
    $term->setRequired("Zadej datum a čas.");
    $term->addRule([$term, "validateDateTime"], "Neplatné datum.");
    $term->setValue($default);
    $form->addComponent($term, "term");
    $form->addSubmit("submit", "Změnit");
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function validate(Form $form, array $values): void {
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