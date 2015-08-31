<?php
namespace Nexendrie\Model\DI;

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
    $builder->addDefinition($this->prefix("addEditNews"))
      ->setFactory("Nexendrie\Forms\AddEditNewsFormFactory");
    $builder->addDefinition($this->prefix("addEditPoll"))
      ->setFactory("Nexendrie\Forms\AddEditPollFormFactory");
  }
}
?>