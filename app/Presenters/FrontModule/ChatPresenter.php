<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Guild;
use Nexendrie\Model\Monastery;
use Nexendrie\Model\NotInMonasteryException;
use Nexendrie\Chat\ITownChatControlFactory;
use Nexendrie\Chat\TownChatControl;
use Nexendrie\Chat\IMonasteryChatControlFactory;
use Nexendrie\Chat\MonasteryChatControl;
use Nexendrie\Chat\IOrderChatControlFactory;
use Nexendrie\Chat\OrderChatControl;
use Nexendrie\Chat\IGuildChatControlFactory;
use Nexendrie\Chat\GuildChatControl;
use Nexendrie\Model\Order;

/**
 * ChatPresenter
 *
 * @author Jakub Konečný
 */
final class ChatPresenter extends BasePresenter
{
    protected bool $cachingEnabled = false;

    public function __construct(
        private readonly Monastery $monasteryModel,
        private readonly Order $orderModel,
        private readonly Guild $guildModel
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();
        $this->requiresLogin();
        $this->mustNotBeBanned();
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
        $this->template->chatRefreshRate = 1;
    }

    protected function createComponentTownChat(ITownChatControlFactory $factory): TownChatControl
    {
        return $factory->create();
    }

    public function actionMonastery(): void
    {
        try {
            $this->monasteryModel->getByUser();
        } catch (NotInMonasteryException) {
            $this->flashMessage("Nejsi v klášteře.");
            $this->redirect("Homepage:");
        }
    }

    protected function createComponentMonasteryChat(IMonasteryChatControlFactory $factory): MonasteryChatControl
    {
        return $factory->create();
    }

    public function actionOrder(): void
    {
        $order = $this->orderModel->getUserOrder();
        if ($order === null) {
            $this->flashMessage("Nejsi v řádu.");
            $this->redirect("Homepage:");
        }
    }

    protected function createComponentOrderChat(IOrderChatControlFactory $factory): OrderChatControl
    {
        return $factory->create();
    }

    public function actionGuild(): void
    {
        $guild = $this->guildModel->getUserGuild();
        if ($guild === null) {
            $this->flashMessage("Nejsi v cechu.");
            $this->redirect("Homepage:");
        }
    }

    protected function createComponentGuildChat(IGuildChatControlFactory $factory): GuildChatControl
    {
        return $factory->create();
    }
}
