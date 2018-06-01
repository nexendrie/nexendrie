<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\GuildNameInUseException,
    Nextras\Orm\Entity\ToArrayConverter;

/**
 * Factory for form ManageGuild
 *
 * @author Jakub Konečný
 */
final class ManageGuildFormFactory {
  /** @var \Nexendrie\Model\Guild */
  protected $model;
  /** @var \Nexendrie\Model\Skills */
  protected $skillsModel;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  private $id;
  
  public function __construct(\Nexendrie\Model\Guild $model, \Nexendrie\Model\Skills $skillsModel, \Nette\Security\User $user) {
    $this->model = $model;
    $this->skillsModel = $skillsModel;
    $this->user = $user;
  }
  
  protected function getListOfSkills(): array {
    return $this->skillsModel->listOfSkills("work")->fetchPairs("id", "name");
  }
  
  public function create(int $guildId): Form {
    $form = new Form();
    $this->id = $guildId;
    $guild = $this->model->getGuild($this->id);
    $form->addText("name", "Jméno:")
      ->setRequired("Zadej jméno.")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
    $form->addTextArea("description", "Popis:")
      ->setRequired("Zadej popis.");
    $form->addSelect("skill", "Dovednost:", $this->getListOfSkills())
      ->setRequired("Vyber dovednost.");
    $form->addSubmit("submit", "Odeslat");
    $form->setDefaults($guild->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->editGuild($this->id, $values);
    } catch(GuildNameInUseException $e) {
      $form->addError("Zadané jméno je již zabráno.");
    }
  }
}
?>