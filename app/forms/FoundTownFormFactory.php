<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\InsufficientLevelForFoundTownException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\CannotFoundTownException,
    Nexendrie\Model\TownNameInUseException;

/**
 * Factory for form FoundTown
 *
 * @author Jakub Konečný
 */
class FoundTownFormFactory {
  /** @var \Nexendrie\Model\Town */
  protected $model;
  
  function __construct(\Nexendrie\Model\Town $model) {
    $this->model = $model;
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
    $form->addSubmit("submit", "Založit");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   */
  function submitted(Form $form, array $values) {
    try {
      $this->model->found($values);
    } catch(InsufficientLevelForFoundTownException $e) {
      $form->addError("Nejsi šlechtic.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    } catch(CannotFoundTownException $e) {
      $form->addError("Nemáš právo založit město.");
    } catch(TownNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>