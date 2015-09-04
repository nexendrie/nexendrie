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
   * @param SystemSettingsFormFactory $factory
   * @return Form
   */
  protected function createComponentSystemSettingsForm(SystemSettingsFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = array($this, "systemSettingsFormSucceeded");
    return $form;
  }
  
  function systemSettingsFormSucceeded(Form $form, $values) {
    $this->flashMessage("Změny uloženy.");
  }
}
?>