<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Orm\Marriage as MarriageEntity;

/**
 * WeddingControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 * @property-write MarriageEntity $marriage
 */
final class WeddingControl extends \Nette\Application\UI\Control
{
    protected \Nexendrie\Model\Marriage $model;
    protected \Nexendrie\Orm\Model $orm;
    protected \Nette\Security\User $user;
    private MarriageEntity $marriage;

    public function __construct(
        \Nexendrie\Model\Marriage $model,
        \Nexendrie\Orm\Model $orm,
        \Nette\Security\User $user,
        UserProfileLinkControlFactory $userProfileLinkControlFactory
    ) {
        $this->model = $model;
        $this->orm = $orm;
        $this->user = $user;
        $this->addComponent($userProfileLinkControlFactory->create(), "userProfileLink");
    }

    protected function setMarriage(MarriageEntity $marriage): void
    {
        $this->marriage = $marriage;
    }

    /**
     * @return string[]
     */
    private function getTexts(): array
    {
        $texts = [];
        return $texts;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . "/wedding.latte");
        $this->template->marriage = $this->marriage;
        $this->template->texts = $this->getTexts();
        $this->template->render();
    }
}
