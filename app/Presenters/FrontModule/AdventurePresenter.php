<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\AdventureControlFactory;
use Nexendrie\Components\AdventureControl;
use Nexendrie\Model\Adventure;

/**
 * Presenter Adventure
 *
 * @author Jakub Konečný
 */
final class AdventurePresenter extends BasePresenter
{
    protected bool $cachingEnabled = false;

    public function __construct(private readonly Adventure $model)
    {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();
        $this->requiresLogin();
        $this->mustNotBeBanned();
        if ($this->user->identity->level === 50) {
            $this->flashMessage("Sedláci nemohou podnikat dobrodružství.");
            $this->redirect("Homepage:");
        }
    }

    public function actionDefault(): void
    {
        if ($this->model->getCurrentAdventure() !== null) {
            return;
        } elseif ($this->model->canDoAdventure()) {
            $this->redirect("list");
        }
        $this->flashMessage("Musíš počkat před dalším dobrodružstvím.");
        $this->redirect("Homepage:");
    }

    protected function createComponentAdventure(AdventureControlFactory $factory): AdventureControl
    {
        return $factory->create();
    }

    public function actionMounts(int $id): void
    {
        if ($this->model->getCurrentAdventure() !== null) {
            $this->redirect("default");
        }
        $this->template->adventure = $id;
    }
}
