<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form ManageGuild
 *
 * @author Jakub Konečný
 */
class ManageGuildFormFactory {
  /** @var \Nexendrie\Model\Guild */
  protected $model;
  /** @var \Nexendrie\Model\Skills */
  protected $skillsModel;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  private $id;
  
  function __construct(\Nexendrie\Model\Guild $model, \Nexendrie\Model\Skills $skillsModel, \Nette\Security\User $user) {
    $this->model = $model;
    $this->skillsModel = $skillsModel;
    $this->user = $user;
  }
  
  protected function getListOfSkills() {
    return $this->skillsModel->listOfSkills("work")->fetchPairs("id", "name");
  }
  
  /**
   * @param int $guildId
   * @return Form
   */
  function create($guildId) {
    $form = new Form;
    $this->id = $guildId;
    $guild = $this->model->getGuild($this->id);
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSelect("skill", "Dovednost:", $this->getListOfSkills())
      ->setPrompt("");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($guild->dummyArray());
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @return void
   */
  function submitted(Form $form) {
    $this->model->editGuild($this->id, $form->getValues(true));
  }
}
?>