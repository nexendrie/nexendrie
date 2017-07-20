<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\SystemSettingsFormFactory,
    Nette\Application\UI\Form;

/**
 * Presenter Settings
 *
 * @author Jakub Konečný
 */
class SettingsPresenter extends BasePresenter {
  public function actionDefault(): void {
    $this->requiresPermissions("site", "settings");
  }
  
  protected function createComponentSystemSettingsForm(SystemSettingsFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>