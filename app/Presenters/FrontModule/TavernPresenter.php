<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\TavernControlFactory;
use Nexendrie\Components\TavernControl;

/**
 * Presenter Tavern
 *
 * @author Jakub Konečný
 */
final class TavernPresenter extends BasePresenter
{
    protected bool $cachingEnabled = false;

    protected function startup(): void
    {
        parent::startup();
        $this->mustNotBeTravelling();
    }

    protected function createComponentTavern(TavernControlFactory $factory): TavernControl
    {
        return $factory->create();
    }
}
