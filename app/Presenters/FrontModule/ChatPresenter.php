<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\NotInMonasteryException;
use Nexendrie\Chat\ITownChatControlFactory;
use Nexendrie\Chat\TownChatControl;
use Nexendrie\Chat\IMonasteryChatControlFactory;
use Nexendrie\Chat\MonasteryChatControl;
use Nexendrie\Chat\IOrderChatControlFactory;
use Nexendrie\Chat\OrderChatControl;
use Nexendrie\Chat\IGuildChatControlFactory;
use Nexendrie\Chat\GuildChatControl;

/**
 * ChatPresenter
 *
 * @author Jakub Konečný
 */
final class ChatPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Monastery */
  protected $monasteryModel;
  /** @var \Nexendrie\Model\Order */
  protected $orderModel;
  /** @var \Nexendrie\Model\Guild */
  protected $guildModel;
  
  public function __construct(\Nexendrie\Model\Monastery $monasteryModel, \Nexendrie\Model\Order $orderModel, \Nexendrie\Model\Guild $guildModel) {
    parent::__construct();
    $this->monasteryModel = $monasteryModel;
    $this->orderModel = $orderModel;
    $this->guildModel = $guildModel;
  }
  
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