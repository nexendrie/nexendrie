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
    $builder->addDefinition($this->prefix("newMessage"))
      ->setFactory("Nexendrie\Forms\NewMessageFormFactory");
    $builder->addDefinition($this->prefix("register"))
      ->setFactory("Nexendrie\Forms\RegisterFormFactory");
    $builder->addDefinition($this->prefix("login"))
      ->setFactory("Nexendrie\Forms\LoginFormFactory");
    $builder->addDefinition($this->prefix("userSettings"))
      ->setFactory("Nexendrie\Forms\UserSettingsFormFactory");
    $builder->addDefinition($this->prefix("addComment"))
      ->setFactory("Nexendrie\Forms\AddCommentFormFactory");
  }
}
?>