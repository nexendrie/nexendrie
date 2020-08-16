<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\ThemesManager;
use Nexendrie\Model\UserManager;
use Nette\Security\User;
use Nexendrie\Model\SettingsException;
use Nexendrie\Orm\User as UserEntity;

/**
 * Factory for form UserSettings
 *
 * @author Jakub Konečný
 */
final class UserSettingsFormFactory {
  protected UserManager $model;
  protected User $user;
  protected ThemesManager $themesManager;

  public function __construct(UserManager $model, User $user, ThemesManager $themesManager) {
    $this->model = $model;
    $this->user = $user;
    $this->themesManager = $themesManager;
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addGroup("Účet");
    $form->addText("publicname", "Zobrazované jméno:")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25)
      ->setRequired("Zadej jméno.");
    $form->addText("email", "E-mail:")
      ->addRule(Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.");
    $form->addRadioList("gender", "Pohlaví:", UserEntity::getGenders())
      ->setRequired("Vyber pohlaví.");
    $form->addSelect("style", "Vzhled stránek:", $this->themesManager->getList());
    $form->addGroup("Heslo")
      ->setOption("description", "Současné a nové heslo vyplňujte jen pokud ho chcete změnit.");
    $passwordOld = $form->addPassword("password_old", "Současné heslo:");
    $passwordNew = $form->addPassword("password_new", "Nové heslo:");
    $passwordOld->addConditionOn($passwordNew, Form::FILLED)
      ->setRequired("Musíš zadat současné heslo.");
    $form->addPassword("password_check", "Nové heslo (kontrola):")
      ->addConditionOn($passwordNew, Form::FILLED)
      ->setRequired("Musíš znovu zadat nové heslo.")
      ->addRule(Form::EQUAL, "Hesla se neshodují.", $form["password_new"]);
    $form->setCurrentGroup(null);
    $form->addSubmit("save", "Uložit změny");
    $form->setDefaults($this->model->getSettings());
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    try {
      $this->model->changeSettings($values);
    } catch(SettingsException $e) {
      if($e->getCode() === UserManager::REG_DUPLICATE_NAME) {
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