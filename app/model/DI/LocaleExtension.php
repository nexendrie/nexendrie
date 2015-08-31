<?php
namespace Nexendrie\Model\DI;

/**
 * Locale Extension for DIC
 *
 * @author Jakub Konečný
 */
class LocaleExtension extends \Nette\DI\CompilerExtension {
  /** @var array */
  public $defaults = array(
    "dateFormat" => "j.n.Y",
    "dateTimeFormat" => "j.n.Y G:i",
    "plural" => array(
      0 => 1, "2-4", 5
    )
  );
  
  /**
   * @return void
   */
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    $builder = $this->getContainerBuilder();
    $builder->addDefinition("nexendrie.locale")
      ->setFactory("Nexendrie\Model\Locale", array($config));
  }
}
?>