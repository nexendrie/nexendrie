<?php
namespace Nexendrie\Forms\DI;

/**
 * Form Extension for DIC
 *
 * @author Jakub Konečný
 */
class FormExtension extends \Nette\DI\CompilerExtension {
  /**
   * @return void
   */
  function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $forms = array(
      "addEditNews", "addEditPoll", "newMessage", "register", "login",
      "userSettings", "addComment", "editGroup", "systemSettings", "editUser"
    );
    foreach($forms as $form) {
      $builder->addDefinition($this->prefix($form))
        ->setFactory("Nexendrie\Forms\\" . ucfirst($form) . "FormFactory");
    }
  }
}
?>