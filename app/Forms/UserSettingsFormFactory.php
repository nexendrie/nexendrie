<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nette\Utils\Finder,
    Nette\Neon\Neon,
    Nette\Utils\Arrays,
    Nexendrie\Model\UserManager,
    Nette\Security\User,
    Nexendrie\Model\SettingsException,
    Nexendrie\Orm\User as UserEntity;

/**
 * Factory for form UserSettings
 *
 * @author Jakub Konečný
 * @property-write User $user
 */
class UserSettingsFormFactory {
  /** @var UserManager */
  protected $model;
  /** @var User */
  protected $user;
  
  function __construct(UserManager $model, User $user) {
    $this->model = $model;
    $this->user = $user;
    $this->model->user = $this->user;
  }
  
  /**
   * Gets list of styles
   * 
   * @return array
   */
  static function getStylesList(): array {
    $styles = [];
    $dir = __DIR__ . "/../../styles";
    $file = file_get_contents("$dir/list.neon");
    $list = Neon::decode($file);
    foreach(Finder::findFiles("*.css")->in($dir) as $style) {
      $key = $style->getBaseName(".css");
      $value = Arrays::get($list, $key, $key);
      $styles[$key] = $value;
    }
    return $styles;
  }
  
  /**
   * @return Form
   */
  function create(): Form {
    $form = new Form;
    $form->addGroup("Účet");
    $form->addText("publicname", "Zobrazované jméno:")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25)
      ->setRequired("Zadej jméno.");
    $form->addText("email", "E-mail:")
      ->addRule(Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.");
    $form->addRadioList("gender", "Pohlaví:", UserEntity::getGenders())
      ->setRequired("Vyber pohlaví.");
    $form->addRadioList("style", "Vzhled stránek:", self::getStylesList());
    $form->addCheckbox("infomails", "Posílat informační e-maily");
    $form->addGroup("Heslo")
      ->setOption("description", "Současné a nové heslo vyplňujte jen pokud ho chcete změnit.");
    $form->addPassword("password_old", "Současné heslo:");
    $form->addPassword("password_new", "Nové heslo:");
    $form->addPassword("password_check", "Nové heslo (kontrola):");
    $form->setCurrentGroup(NULL);
    $form->addSubmit("save", "Uložit změny");
    $form->setDefaults($this->model->getSettings());
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function validate(Form $form, array $values) {
    if(empty($values["password_old"]) AND !empty($values["password_new"])) {
      $form->addError("Musíš zadat současné heslo.");
    }
    if($values["password_new"] != $values["password_check"]) {
      $form->addError("Hesla se neshodují.");
    }
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values) {
    try {
      $this->model->changeSettings($values);
    } catch (SettingsException $e) {
      if($e->getCode() === UserManager::REG_DUPLICATE_USERNAME) {
        $form->addError("Zvolené jméno je už zabráno.");
      }
      if($e->getCode() === UserManager::REG_DUPLICATE_EMAIL) {
        $form->addError("Zadaný e-mail je už používán.");
      }
      if($e->getCode() === UserManager::SET_INVALID_PASSWORD) {
        $form->addError("Neplatné heslo.");
      }
    }
  }
}
?>