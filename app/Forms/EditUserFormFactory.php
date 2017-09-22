<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\UserManager,
    Nexendrie\Model\Group,
    Nexendrie\Model\Town;

/**
 * Factory for form EditUser
 *
 * @author Jakub Konečný
 */
class EditUserFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var UserManager */
  protected $model;
  /** @var Group */
  protected $groupModel;
  /** @var Town */
  protected $townModel;
  /** @var int */
  protected $uid;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, UserManager $model, Group $groupModel, Town $townModel) {
    $this->orm = $orm;
    $this->model = $model;
    $this->groupModel = $groupModel;
    $this->townModel = $townModel;
  }
  
  protected function getListOfGroups(): array {
    $return = [];
    $groups = $this->groupModel->listOfGroups();
    foreach($groups as $group) {
      $return[$group->id] = $group->name;
    }
    return $return;
  }
  
  protected function getListOfTowns(): array {
    return $this->townModel->listOfTowns()->fetchPairs("id", "name");
  }
  
  /**
   * @throws \Nette\ArgumentOutOfRangeException
   */
  protected function getDefaultValues(): array {
    $user = $this->orm->users->getById($this->uid);
    if(!$user) {
      throw new \Nette\ArgumentOutOfRangeException("User with specified id does not exist.");
    }
    return [
      "username" => $user->username,
      "publicname" => $user->publicname,
      "group" => $user->group->id,
      "town" => $user->town->id
    ];
  }
  
  public function create(int $uid): Form {
    $form = new Form();
    $this->uid = $uid;
    $form->addText("username", "Uživatelské jméno:")
      ->setRequired("Uživatelské jméno nesmí být prázdné");
    $form->addText("publicname", "Zobrazované jméno:")
      ->setRequired("Zobrazované jméno nesmí být prázdné");
    $form->addSelect("group", "Skupina:", $this->getListOfGroups())
      ->setRequired("Vyber skupinu.");
    $form->addSelect("town", "Město", $this->getListOfTowns())
       ->setRequired("Vyber město.");
    $form->setDefaults($this->getDefaultValues());
    $form->addSubmit("submit", "Uložit");
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function validate(Form $form, array $values): void {
    if($values["group"] == 0 AND $this->uid != 0) {
      $form->addError("Neplatná skupina.");
    }
  }
  
  public function process(Form $form, array $values): void {
    $this->model->edit($this->uid, $values);
  }
}
?>