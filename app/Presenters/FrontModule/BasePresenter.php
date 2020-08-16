<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form;
use Nexendrie\Chat;
use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\NewChatMessageFormFactory;

/**
 * Parent of all front presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nexendrie\Presenters\BasePresenter {
  /**
   * The user must be logged in to see a page
   */
  protected function requiresLogin(): void {
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("K zobrazení této stránky musíš být přihlášen.");
      $this->redirect("User:login", ["backlink" => $this->storeRequest()]);
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
    if($this->user->isLoggedIn() && $this->user->identity->travelling) {
      $this->flashMessage("Toto nemůžeš dělat, když jsi na cestách.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentNewChatMessageForm(NewChatMessageFormFactory $factory): Form {
    $chat = strtolower((string) \Nette\Utils\Strings::after($this->name, ":"));
    switch($chat) {
      case "town":
        /** @var Chat\ITownChatControlFactory $chatFactory */
        $chatFactory = $this->context->getByType(Chat\ITownChatControlFactory::class);
        break;
      case "monastery":
        /** @var Chat\IMonasteryChatControlFactory $factory */
        $chatFactory = $this->context->getByType(Chat\IMonasteryChatControlFactory::class);
        break;
      case "guild":
        /** @var Chat\IGuildChatControlFactory $factory */
        $chatFactory = $this->context->getByType(Chat\IGuildChatControlFactory::class);
        break;
      case "order":
        /** @var Chat\IOrderChatControlFactory $factory */
        $chatFactory = $this->context->getByType(Chat\IOrderChatControlFactory::class);
        break;
      default:
        throw new \RuntimeException("Invalid chat $chat.");
    }
    /** @var ChatControl $chat */
    $chat = $chatFactory->create();
    /** @var NewChatMessageFormFactory $factory */
    return $factory->create($chat);
  }
}
?>