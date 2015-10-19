<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Forms\SystemSettingsFormFactory,
    Nette\Application\UI\Form;

/**
 * Presenter Settings
 *
 * @author Jakub Konečný
 */
class SettingsPresenter extends BasePresenter {
  /**
   * @return void
   */
  function actionDefault() {
    $this->requiresPermissions("site", "settings");
  }
  
  /**
   * @param SystemSettingsFormFactory $factory
   * @return Form
   */
  protected function createComponentSystemSettingsForm(SystemSettingsFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, $values) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>