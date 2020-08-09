<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\UserManager;
use Nexendrie\Model\Town;

/**
 * Factory for form EditUser
 *
 * @author Jakub Konečný
 */
final class EditUserFormFactory {
  protected \Nexendrie\Orm\Model $orm;
  protected UserManager $model;
  protected Town $townModel;
  protected int $uid;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, UserManager $model, Town $townModel) {
    $this->orm = $orm;
    $this->model = $model;
    $this->townModel = $townModel;
  }
  
  protected function getListOfGroups(int $uid): array {
    if($uid === 0) {
      $groups = $this->orm->groups->findBy(["id" => 0]);
    } else {
      $groups = $this->orm->groups->findBy(["level>" => 0, "id!=" => 0]);
    }
    return $groups->fetchPairs("id", "name");
  }
  
  protected function getListOfTowns(): array {
    return $this->townModel->listOfTowns()->fetchPairs("id", "name");
  }
  
  /**
   * @throws \Nette\ArgumentOutOfRangeException
   */
  protected function getDefaultValues(): array {
    $user = $this->orm->users->getById($this->uid);
    if($user === null) {
      throw new \Nette\ArgumentOutOfRangeException("User with specified id does not exist.");
    }
    return [
      "publicname" => $user->publicname,
      "group" => $user->group->id,
      "town" => $user->town->id
    ];
  }
  
  public function create(int $uid): Form {
    $form = new Form();
    $this->uid = $uid;
    $groups = $this->getListOfGroups($uid);
    $form->addText("publicname", "Zobrazované jméno:")
      ->setRequired("Zobrazované jméno nesmí být prázdné");
    $form->addSelect("group", "Skupina:", $groups)
      ->setRequired("Vyber skupinu.");
    $form->addSelect("town", "Město", $this->getListOfTowns())
      ->setRequired("Vyber město.");
    $form->setDefaults($this->getDefaultValues());
    $form->addSubmit("submit", "Uložit");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    $this->model->edit($this->uid, $values);
  }
}
?>