<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form,
    Nexendrie\Chat,
    HeroesofAbenez\Chat\ChatControl;

/**
 * Parent of all front presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nexendrie\Presenters\BasePresenter {
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  
  public function injectProfileModel(\Nexendrie\Model\Profile $profileModel): void {
    $this->profileModel = $profileModel;
  }
  
  /**
   * The user must be logged in to see a page
   */
  protected function requiresLogin(): void {
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("K zobrazení této stránky musíš být přihlášen.");
      $this->redirect("User:login");
    }
  }
  
  /**
   * The user must not be logged in to see a page
   */
  protected function mustNotBeLoggedIn(): void {
    if($this->user->isLoggedIn()) {
      $this->flashMessage("Už jsi přihlášen.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * The user must not be banned to see a page
   */
  protected function mustNotBeBanned(): void {
    if($this->user->identity->banned) {
      $this->flashMessage("Ještě neskončil tvůj trest.");
      $this->redirect("Prison:");
    }
  }
  
  /**
   * The user must not be on adventure to see a page
   */
  protected function mustNotBeTavelling(): void {
    if($this->user->isLoggedIn() AND $this->user->identity->travelling) {
      $this->flashMessage("Toto nemůžeš dělat, když jsi na cestách.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentNewChatMessageForm(): Form {
    $form = new Form();
    $form->addText("message")
      ->setRequired("Zadej zprávu.");
    $form->addSubmit("send", "Odeslat");
    $chat = strtolower(\Nette\Utils\Strings::after($this->name, ":"));
    switch($chat) {
      case "town":
        /** @var Chat\ITownChatControlFactory $factory */
        $factory = $this->context->getByType(Chat\ITownChatControlFactory::class);
        break;
      case "monastery":
        /** @var Chat\IMonasteryChatControlFactory $factory */
        $factory = $this->context->getByType(Chat\IMonasteryChatControlFactory::class);
        break;
      case "guild":
        /** @var Chat\IGuildChatControlFactory $factory */
        $factory = $this->context->getByType(Chat\IGuildChatControlFactory::class);
        break;
      case "order":
        /** @var Chat\IOrderChatControlFactory $factory */
        $factory = $this->context->getByType(Chat\IOrderChatControlFactory::class);
        break;
      default:
        throw new \RuntimeException("Invalid chat $chat.");
    }
    /** @var ChatControl $chat */
    $chat = $factory->create();
    $form->addComponent($chat, "chat");
    $form->onSuccess[] = function(Form $form, array $values) {
      /** @var ChatControl $chat */
      $chat = $form->getComponent("chat");
      $chat->newMessage($values["message"]);
    };
    return $form;
  }
}
?>