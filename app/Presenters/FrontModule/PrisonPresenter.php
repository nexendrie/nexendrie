<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IPrisonControlFactory;
use Nexendrie\Components\PrisonControl;

/**
 * Presenter Prison
 *
 * @author Jakub Konečný
 */
final class PrisonPresenter extends BasePresenter
{
    protected bool $cachingEnabled = false;

    protected function startup(): void
    {
        parent::startup();
        if (!$this->user->isLoggedIn()) {
            $this->redirect("Homepage:");
        } elseif (!$this->user->identity->banned) {
            $this->redirect("Homepage:");
        }
    }

    protected function createComponentPrison(IPrisonControlFactory $factory): PrisonControl
    {
        return $factory->create();
    }
}
