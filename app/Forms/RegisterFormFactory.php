<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\UserManager;
use Nexendrie\Model\RegistrationException;
use Nexendrie\Model\SettingsRepository;

/**
 * Factory for form Register
 *
 * @author Jakub Konečný
 */
final class RegisterFormFactory {
  protected UserManager $model;
  protected string $registrationToken;
  
  public function __construct(UserManager $model, SettingsRepository $sr) {
    $this->model = $model;
    $this->registrationToken = $sr->settings["registration"]["token"];
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addText("publicname", "Jméno:")
      ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25)
      ->setRequired("Zadej jméno.")
      ->setOption("description", "Toto jméno se zobrazuje ostatním.");
    $form->addText("email", "E-mail:")
      ->addRule(Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.")
      ->setOption("description", "Slouží jako uživatelské jméno.");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo.");
    if($this->registrationToken !== "") {
      $form->addText("token", "Token:")
        ->setRequired()
        ->addRule(Form::EQUAL, "Špatné heslo.", $this->registrationToken)
        ->setOption("description", "Registrace na tomto serveru vyžaduje heslo.");
    }
    $form->addSubmit("register", "Zaregistrovat se");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    unset($values["token"]);
    try {
      $this->model->register($values);
    } catch(RegistrationException $e) {
      if($e->getCode() === UserManager::REG_DUPLICATE_EMAIL) {
        $form->addError("Zadaný e-mail je už používán.");
      }
      if($e->getCode() === UserManager::REG_DUPLICATE_NAME) {
        $form->addError("Zvolené jméno je už zabráno.");
      }
    }
  }
}
?>