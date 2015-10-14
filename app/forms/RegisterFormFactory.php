<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nette\Utils\ArrayHash,
    Nexendrie\Model\UserManager,
    Nexendrie\Model\RegistrationException;

/**
 * Factory for form Register
 *
 * @author Jakub Konečný
 */
class RegisterFormFactory {
  /** @var UserManger */
  protected $model;
  
  function __construct(UserManager $model) {
    $this->model = $model;
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addText("username", "Uživatelské jméno:")
      ->addRule(Form::MAX_LENGTH, "Uživatelské jméno může mít maximálně 25 znaků." , 25)
      ->setRequired("Zadej jméno.")
      ->setOption("description", "Toto jméno se používá pouze pro příhlášení. Jméno, které se zobrazuje ostatním, se mění v Nastavení.");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo.");
    $form->addText("email", "E-mail:")
      ->addRule(Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.");
    $form->addSubmit("register", "Zaregistrovat se");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  function submitted(Form $form) {
    try {
      $this->model->register($form->getValues(true));
    } catch (RegistrationException $e) {
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