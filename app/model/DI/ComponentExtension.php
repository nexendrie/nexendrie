<?php
namespace Nexendrie\Model\DI;

/**
 * ComponentExtension for DIC
 *
 * @author Jakub Konečný
 */
class ComponentExtension extends \Nette\DI\CompilerExtension {
  /**
   * @return void
   */
  function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("poll"))
      ->setImplement("Nexendrie\Components\PollControlFactory");
    $builder->addDefinition($this->prefix("shop"))
      ->setImplement("Nexendrie\Components\ShopControlFactory");
  }
}
?>