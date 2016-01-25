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
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  private $id;
  
  function __construct(\Nexendrie\Model\Guild $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->user = $user;
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
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($guild->toArray());
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