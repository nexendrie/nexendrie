<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nette\Utils\ArrayHash,
    Nexendrie\Model\UserManager,
    Nexendrie\Model\Group;

/**
 * Factory for form EditUser
 *
 * @author Jakub Konečný
 */
class EditUserFormFactory extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var UserManager */
  protected $model;
  /** @var Group */
  protected $groupModel;
  /** @var int */
  protected $uid;
  
  function __construct(\Nexendrie\Orm\Model $orm, UserManager $model, Group $groupModel) {
    $this->orm = $orm;
    $this->model = $model;
    $this->groupModel = $groupModel;
  }
  
  /**
   * @return array
   */
  protected function getListOfGroups() {
    $return = array();
    $groups = $this->groupModel->listOfGroups();
    foreach($groups as $group) {
      $return[$group->id] = $group->name;
    }
    return $return;
  }
  
  /**
   * @return array
   * @throws \Nette\ArgumentOutOfRangeException
   */
  protected function getDefaultValues() {
    $user = $this->orm->users->getById($this->uid);
    if(!$user) throw new \Nette\ArgumentOutOfRangeException("User with specified id does not exist.");
    return array(
      "username" => $user->username,
      "publicname" => $user->publicname,
      "group" => $user->group->id,
      "banned" => (bool) $user->banned
    );
  }
  
  /**
   * @param int $uid
   * @return Form
   */
  function create($uid) {
    $form = new Form;
    $this->uid = (int) $uid;
    $form->addText("username", "Uživatelské jméno:")
      ->setRequired("Uživatelské jméno nesmí být prázdné");
    $form->addText("publicname", "Zobrazované jméno:")
      ->setRequired("Zobrazované jméno nesmí být prázdné");
    $form->addSelect("group", "Skupina:", $this->getListOfGroups());
    $form->addCheckbox("banned", "Zablokován");
    $form->setDefaults($this->getDefaultValues());
    $form->addSubmit("submit", "Uložit");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @param ArrayHash $values
   * @return void
   */
  function submitted(Form $form, ArrayHash $values) {
    $this->model->edit($this->uid, $values);
  }
}
?>