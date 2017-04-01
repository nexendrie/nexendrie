<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Model\UserManager,
    Nexendrie\Model\RegistrationException;
use Nexendrie\Model\SettingsRepository;

/**
 * Factory for form Register
 *
 * @author Jakub Konečný
 */
class RegisterFormFactory {
  /** @var UserManager */
  protected $model;
  /** @var SettingsRepository */
  protected $sr;
  
  function __construct(UserManager $model, SettingsRepository $sr) {
    $this->model = $model;
    $this->sr = $sr;
  }
  
  /**
   * @return Form
   */
  function create(): Form {
    $form = new Form;
    $form->addText("username", "Uživatelské jméno:")
      ->addRule(Form::MAX_LENGTH, "Uživatelské jméno může mít maximálně 25 znaků.", 25)
      ->setRequired("Zadej jméno.")
      ->setOption("description", "Toto jméno se používá pouze pro příhlášení. Jméno, které se zobrazuje ostatním, se mění v Nastavení.");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo.");
    $form->addText("email", "E-mail:")
      ->addRule(Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.");
    if($this->sr->settings["registration"]["token"]) {
      $form->addText("token", "Token:")
        ->setRequired()
        ->addRule(Form::EQUAL, "Špatné heslo.", $this->sr->settings["registration"]["token"])
        ->setOption("description", "Registrace na tomto serveru vyžaduje heslo.");
    }
    $form->addSubmit("register", "Zaregistrovat se");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function process(Form $form, array $values): void {
    try {
      $this->model->register($values);
    } catch(RegistrationException $e) {
      if($e->getCode() === UserManager::REG_DUPLICATE_USERNAME) {
        $form->addError("Zvolené uživatelské jméno je už zabráno.");
      }
      if($e->getCode() === UserManager::REG_DUPLICATE_EMAIL) {
        $form->addError("Zadaný e-mail je už používán.");
      }
    }
  }
}
?>