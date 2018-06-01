<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\UserItem as UserItemEntity,
    Nexendrie\Orm\Message as MessageEntity,
    Nexendrie\Orm\Item as ItemEntity;

/**
 * Factory for form Gift
 *
 * @author Jakub Konečný
 */
final class GiftFormFactory {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  public function __construct(\Nexendrie\Model\Locale $localeModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->localeModel = $localeModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of users
   * 
   * @return array id => publicname
   */
  protected function getUsersList(): array {
    return $this->orm->users->findBy(
        ["id>" => 0]
    )->fetchPairs("id", "publicname");
  }
  
  /**
   * Get list of items
   * 
   * @return array id => name
   */
  protected function getItemsList(): array {
    return $this->orm->items->findAll()->fetchPairs("id", "name");
  }
  
  public function create(): Form {
    $form = new Form();
    $form->addSelect("user", "Uživatel:", $this->getUsersList())
      ->setRequired("Vyber uživatele.");
    $form->addText("money", "Peníze:")
      ->addRule(Form::INTEGER, "Zadej celé číslo.")
      ->addRule(Form::RANGE, "Zadej číslo v rozmezí 0-2000.", [0, 2000])
      ->setValue(0)
      ->setRequired("Zadej částku.");
    $form->addSelect("item", "Věc:", $this->getItemsList())
      ->setPrompt("-");
    $form->addTextArea("message", "Zpráva pro příjemce:");
    $form->addSubmit("submit", "Darovat");
    $form->onValidate[] = [$this, "validate"];
    $form->onSuccess[] = [$this, "process"];
    return $form;
  }
  
  public function validate(Form $form, array $values): void {
    if($values["money"] === 0 AND is_null($values["item"])) {
      $form->addError("Musíš zadat částku (a)nebo vybrat věc.");
    }
    $money = $values["money"];
    if($money > 0) {
      /** @var \Nexendrie\Orm\User $queen */
      $queen = $this->orm->users->getById(0);
      if($queen->money < $money) {
        $form->addError("V pokladně není požadované množství peněz.");
        return;
      }
    }
  }
  
  protected function composeMessage(int $money, string $item): string {
    $message = "Dostal jsi ";
    if($money > 0) {
      $message .= $this->localeModel->money($money);
    }
    if($money > 0 AND strlen($item) > 0) {
      $message .= " a ";
    }
    if(strlen($item) > 0) {
      $message .= $item;
    }
    $message .= ".";
    return $message;
  }
  
  public function process(Form $form, array $values): void {
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($values["user"]);
    /** @var \Nexendrie\Orm\User $queen */
    $queen = $this->orm->users->getById(0);
    $money = $values["money"];
    $itemName = "";
    if($money > 0) {
      $queen->money -= $money;
      $user->money += $money;
      $this->orm->users->persist($queen);
      $this->orm->users->persist($user);
    }
    if($values["item"]) {
      /** @var ItemEntity $item */
      $item = $this->orm->items->getById($values["item"]);
      $row = $this->orm->userItems->getByUserAndItem($user->id, $item->id);
      if(is_null($row)) {
        $row = new UserItemEntity();
        $row->user = $user;
        $row->item = $item;
        $row->amount = 0;
      }
      $row->amount++;
      $this->orm->userItems->persist($row, false);
      $itemName = $item->name;
    }
    $messageText = $values["message"];
    if(count($messageText) < 1) {
      $messageText = $this->composeMessage($money, $itemName);
    }
    $message = new MessageEntity();
    $message->from = $this->orm->users->getById($this->user->id);
    $message->to = $user;
    $message->subject = "Dárek";
    $message->text = $messageText;
    $message->sent = time();
    $this->orm->messages->persist($message, false);
    $this->orm->flush();
  }
}
?>