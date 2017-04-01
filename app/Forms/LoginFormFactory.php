<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nette\Security\User,
    Nette\Security\AuthenticationException,
    Nexendrie\Model\UserManager;

/**
 * Factory for form Login
 *
 * @author Jakub Konečný
 */
class LoginFormFactory {
  /** @var User */
  protected $user;
  
  function __construct(User $user) {
    $this->user = $user;
  }
  
  /**
   * @return Form
   */
  function create(): Form {
    $form = new Form;
    $form->addText("username", "Uživatelské jméno:")
      ->setRequired("Zadej jméno.");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo.");
    $form->addSubmit("login", "Přihlásit se");
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values): void {
    try {
      $this->user->login($values["username"], $values["password"]);
    } catch(AuthenticationException $e) {
      if($e->getCode() === UserManager::IDENTITY_NOT_FOUND) {
        $form->addError("Neplatné uživatelské jméno.");
      }
      if($e->getCode() === UserManager::INVALID_CREDENTIAL) {
        $form->addError("Neplatné heslo.");
      }
    }
  }
}
?>