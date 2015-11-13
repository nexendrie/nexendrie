<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\CannotBuildCastleException,
    Nexendrie\Model\CannotBuildMoreCastlesException,
    Nexendrie\Model\CastleNameInUseException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * Factory for form BuildCastle
 *
 * @author Jakub Konečný
 */
class BuildCastleFormFactory {
  /** @var \Nexendrie\Model\Castle */
  protected $model;
  
  function __construct(\Nexendrie\Model\Castle $model) {
    $this->model = $model;
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků", 20);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSubmit("submit", "Postavit");
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
      $this->model->build($values);
    } catch(CannotBuildCastleException $e) {
      $form->addError("Nemůže stavět hrad.");
    } catch(CannotBuildMoreCastlesException $e) {
      $form->addError("Můžeš postavit jen 1 hrad.");
    } catch(CastleNameInUseException $e) {
      $form->addError("Zadané jméno je již zabrané.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    }
  }
}
?>