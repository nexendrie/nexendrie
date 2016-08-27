<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\Punishment;

/**
 * Factory for form BanUser
 *
 * @author Jakub Konečný
 */
class BanUserFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var int */
  protected $userId;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @param int $userId
   * @return Form
   */
  function create($userId) {
    $this->userId = $userId;
    $form = new Form;
    $form->addTextArea("crime", "Zločin:")
      ->setRequired("Zadej zločin.");
    $form->addText("numberOfShifts", "Počet směn:")
      ->setRequired("Zadej počet směn.")
      ->addRule(Form::INTEGER, "Počet směn musí být celé číslo.")
      ->addRule(Form::RANGE, "Počet směn musí být v rozmezí 1-9999.", [1, 9999]);
    $form->addSubmit("ban", "Uvěznit");
    $form->onSuccess[] = [$this, "submitted"];
    return $form;
  }
  
  /**
   * @param Form $form
   * @return void
   */
  function submitted(Form $form) {
    $values = $form->getValues(true);
    $user = $this->orm->users->getById($this->userId);
    $user->banned = true;
    $punishment = new Punishment;
    $punishment->user = $user;
    $punishment->numberOfShifts = $values["numberOfShifts"];
    $punishment->crime = $values["crime"];
    $this->orm->punishments->persistAndFlush($punishment);
  }
}
?>