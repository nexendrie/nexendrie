<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Guild;
use Nexendrie\Model\Monastery;
use Nexendrie\Model\NotInMonasteryException;
use Nexendrie\Chat\TownChatControlFactory;
use Nexendrie\Chat\TownChatControl;
use Nexendrie\Chat\MonasteryChatControlFactory;
use Nexendrie\Chat\MonasteryChatControl;
use Nexendrie\Chat\OrderChatControlFactory;
use Nexendrie\Chat\OrderChatControl;
use Nexendrie\Chat\GuildChatControlFactory;
use Nexendrie\Chat\GuildChatControl;
use Nexendrie\Model\Order;

/**
 * ChatPresenter
 *
 * @author Jakub Konečný
 */
final class ChatPresenter extends BasePresenter
{
    public function __construct(
        private readonly Monastery $monasteryModel,
        private readonly Order $orderModel,
        private readonly Guild $guildModel
    ) {
        parent::__construct();
        $this->cachingEnabled = false;
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

    protected function createComponentTownChat(TownChatControlFactory $factory): TownChatControl
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

    protected function createComponentMonasteryChat(MonasteryChatControlFactory $factory): MonasteryChatControl
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

    protected function createComponentOrderChat(OrderChatControlFactory $factory): OrderChatControl
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

    protected function createComponentGuildChat(GuildChatControlFactory $factory): GuildChatControl
    {
        return $factory->create();
    }
}
