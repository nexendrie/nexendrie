<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nette\Utils\ArrayHash,
    Nexendrie\Model\UserManager,
    Nexendrie\Model\Group,
    Nexendrie\Model\Town;

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
  /** @var Town */
  protected $townModel;
  
  /** @var int */
  protected $uid;
  
  function __construct(\Nexendrie\Orm\Model $orm, UserManager $model, Group $groupModel, Town $townModel) {
    $this->orm = $orm;
    $this->model = $model;
    $this->groupModel = $groupModel;
    $this->townModel = $townModel;
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
   */
  protected function getListOfTowns() {
    $return = array();
    $towns = $this->townModel->listOfTowns();
    foreach($towns as $town) {
      $return[$town->id] = $town->name;
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
      "town" => $user->town->id
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
    $form->addSelect("group", "Skupina:", $this->getListOfGroups())
      ->setRequired("Vyber skupinu.");
    $form->addSelect("town", "Město", $this->getListOfTowns())
       ->setRequired("Vyber město.");
    $form->setDefaults($this->getDefaultValues());
    $form->addSubmit("submit", "Uložit");
    $form->onValidate[] = array($this, "validate");
    $form->onSuccess[] = function (Form $form, ArrayHash $values) {
      $this->model->edit($this->uid, $values);
    };
    return $form;
  }
  
  function validate(Form $form) {
    $values = $form->getValues(true);
    if($values["group"] == 0 AND $this->uid != 0) $form->addError("Neplatná skupina.");
  }
}
?>