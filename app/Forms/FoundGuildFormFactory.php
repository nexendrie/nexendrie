<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\CannotFoundGuildException,
    Nexendrie\Model\GuildNameInUseException,
    Nexendrie\Model\InsufficientFundsException;

/**
 * Factory for form FoundGuild
 *
 * @author Jakub Konečný
 */
class FoundGuildFormFactory {
  /** @var \Nexendrie\Model\Guild */
  protected $model;
  /** @var \Nexendrie\Model\Skills */
  protected $skillsModel;
  
  function __construct(\Nexendrie\Model\Guild $model, \Nexendrie\Model\Skills $skillsModel) {
    $this->model = $model;
    $this->skillsModel = $skillsModel;
  }
  
  protected function getListOfSkills() {
    return $this->skillsModel->listOfSkills("work")->fetchPairs("id", "name");
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSelect("skill", "Dovednost:", $this->getListOfSkills())
      ->setRequired("Vyber dovednost.");
    $form->addSubmit("submit", "Založit");
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   */
  function submitted(Form $form, array $values) {
    try {
      $this->model->found($values);
    } catch(CannotFoundGuildException $e) {
      $form->addError("Nemůžeš založit cech.");
    } catch(GuildNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    } catch(InsufficientFundsException $e) {
      $form->addError("Nemáš dostatek peněz.");
    }
  }
}
?>