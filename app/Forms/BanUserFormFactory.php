<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Orm\Punishment;
use Nexendrie\Orm\User;

/**
 * Factory for form BanUser
 *
 * @author Jakub Konečný
 */
final class BanUserFormFactory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var int */
  protected $userId;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  public function create(int $userId): Form {
    $this->userId = $userId;
    $form = new Form();
    $form->addTextArea("crime", "Zločin:")
      ->setRequired("Zadej zločin.");
    $form->addText("numberOfShifts", "Počet směn:")
      ->setRequired("Zadej počet směn.")
      ->addRule(Form::INTEGER, "Počet směn musí být celé číslo.")
      ->addRule(Form::RANGE, "Počet směn musí být v rozmezí 1-9999.", [1, 9999]);
    $form->addSubmit("ban", "Uvěznit");
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function process(Form $form, array $values): void {
    /** @var User $user */
    $user = $this->orm->users->getById($this->userId);
    $punishment = new Punishment();
    $punishment->user = $user;
    $punishment->numberOfShifts = $values["numberOfShifts"];
    $punishment->crime = $values["crime"];
    $this->orm->punishments->persistAndFlush($punishment);
  }
}
?>