<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\CannotBuildMonasteryException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\MonasteryNameInUseException;

/**
 * Factory for form BuildMonastery
 *
 * @author Jakub Konečný
 */
class BuildMonasteryFormFactory {
  /** @var \Nexendrie\Model\Monastery */
  protected $model;
  
  function __construct(\Nexendrie\Model\Monastery $model) {
    $this->model = $model;
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno");
    $form->addSubmit("submit", "Založit klášter");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values) {
    try {
      $this->model->build($values["name"]);
    } catch(CannotBuildMonasteryException $e) {
      $form->addError("Nemůžeš postavit klášter.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    } catch(MonasteryNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>