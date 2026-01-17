<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * AchievementsPresenter
 *
 * @author Jakub Konečný
 */
final class AchievementsPresenter extends BasePresenter
{
    protected function startup(): void
    {
        parent::startup();
        $this->requiresLogin();
    }

    public function renderDefault(): never
    {
        $this->redirect("Profile:achievements", ["name" => $this->user->identity->name]);
    }
}
