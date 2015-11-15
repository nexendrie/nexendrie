<?php
namespace Nexendrie\Forms;

use Nette\Application\UI\Form,
    Nexendrie\Orm\UserItem as UserItemEntity,
    Nexendrie\Orm\Message as MessageEntity;

/**
 * Factory for form Gift
 *
 * @author Jakub Konečný
 */
class GiftFormFactory {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Locale $localeModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->localeModel = $localeModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of users
   * 
   * @return array id => publicname
   */
  protected function getUsersList() {
    return $this->orm->users->findBy(
      array("id>" => 0)
    )->fetchPairs("id", "publicname");
  }
  
  /**
   * Get list of items
   * 
   * @return array id => name
   */
  protected function getItemsList() {
    return $this->orm->items->findAll()->fetchPairs("id", "name");
  }
  
  /**
   * @return Form
   */
  function create() {
    $form = new Form;
    $form->addSelect("user", "Uživatel:", $this->getUsersList())
      ->setRequired("Vyber uživatele.");
    $form->addText("money", "Peníze:")
      ->addRule(Form::INTEGER, "Zadej celé číslo.")
      ->addRule(Form::RANGE, "Zadej číslo v rozmezí 0-2000.", array(0, 2000))
      ->setValue(0);
    $form->addSelect("item", "Věc:", $this->getItemsList())
      ->setPrompt("-");
    $form->addTextArea("message", "Zpráva pro příjemce:");
    $form->addSubmit("submit", "Darovat");
    $form->onValidate[] = array($this, "validate");
    $form->onSuccess[] = array($this, "submitted");
    return $form;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function validate(Form $form, array $values) {
    if($values["money"] === 0 AND $values["item"] === NULL) {
      $form->addError("Musíš zadat částku (a)nebo vybrat věc.");
    }
    $money = $values["money"];
    if($money > 0) {
      $queen = $this->orm->users->getById(0);
      if($queen->money < $money) {
        $form->addError("V pokladně není požadované množství peněz.");
        return;
      }
    }
  }
  
  /**
   * @param int $money
   * @param string $item
   * @return string
   */
  protected function composeMessage($money, $item) {
    $message = "Dostal jsi ";
    if($money > 0) $message .= $this->localeModel->money($money);
    if($money > 0 AND strlen($item) > 0) $message .= " a ";
    if(strlen($item) > 0) $message .= " $item";
    $message .= ".";
    return $message;
  }
  
  /**
   * @param Form $form
   * @param array $values
   * @return void
   */
  function submitted(Form $form, array $values) {
    $user = $this->orm->users->getById($values["user"]);
    $queen = $this->orm->users->getById(0);
    $money = $values["money"];
    $filledMessage = (strlen($values["message"]) > 0);
    $itemName = "";
    if($money > 0) {
      $queen->money -= $money;
      $user->money += $money;
      $this->orm->users->persist($queen);
      $this->orm->users->persist($user);
    }
    if($values["item"]) {
      $item = $this->orm->items->getById($values["item"]);
      $row = $this->orm->userItems->getByUserAndItem($user->id, $item->id);
      if($row) {
        $row->amount++;
      } else {
        $row = new UserItemEntity;
        $row->user = $user;
        $row->item = $item;
      }
      $this->orm->userItems->persist($row, false);
      $itemName = $item->name;
    }
    if(!$filledMessage) $messageText = $this->composeMessage($money, $itemName);
    else $messageText = $values["message"];
    $message = new MessageEntity;
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