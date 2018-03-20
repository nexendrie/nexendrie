<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\NotInMonasteryException,
    Nexendrie\Chat\ITownChatControlFactory,
    Nexendrie\Chat\TownChatControl,
    Nexendrie\Chat\IMonasteryChatControlFactory,
    Nexendrie\Chat\MonasteryChatControl,
    Nexendrie\Chat\IOrderChatControlFactory,
    Nexendrie\Chat\OrderChatControl,
    Nexendrie\Chat\IGuildChatControlFactory,
    Nexendrie\Chat\GuildChatControl;

/**
 * ChatPresenter
 *
 * @author Jakub Konečný
 */
class ChatPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Monastery @autowire */
  protected $monasteryModel;
  /** @var \Nexendrie\Model\Order @autowire */
  protected $orderModel;
  /** @var \Nexendrie\Model\Guild @autowire */
  protected $guildModel;
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  protected function beforeRender(): void {
    parent::beforeRender();
    $this->template->chatRefreshRate = 1;
  }
  
  protected function createComponentTownChat(ITownChatControlFactory $factory): TownChatControl {
    return $factory->create();
  }
  
  public function actionMonastery() {
    try {
      $this->monasteryModel->getByUser();
    } catch(NotInMonasteryException $e) {
      $this->flashMessage("Nejsi v klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentMonasteryChat(IMonasteryChatControlFactory $factory): MonasteryChatControl {
    return $factory->create();
  }
  
  public function actionOrder() {
    $order = $this->orderModel->getUserOrder();
    if(is_null($order)) {
      $this->flashMessage("Nejsi v řádu.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentOrderChat(IOrderChatControlFactory $factory): OrderChatControl {
    return $factory->create();
  }
  
  public function actionGuild() {
    $guild = $this->guildModel->getUserGuild();
    if(is_null($guild)) {
      $this->flashMessage("Nejsi v cechu.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentGuildChat(IGuildChatControlFactory $factory): GuildChatControl {
    return $factory->create();
  }
}
?>