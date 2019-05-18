<?php
declare(strict_types=1);

namespace Nexendrie\Api\DI;

/**
 * Api Extension for DIC
 *
 * @author Jakub Konečný
 */
final class ApiExtension extends \Nette\DI\CompilerExtension {
  protected $defaults = [
    "transformers" => [],
    "maxDepth" => 2,
  ];

  public function loadConfiguration(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    $builder->addDefinition($this->prefix("entityConverter"))
      ->setFactory(\Nexendrie\Api\EntityConverter::class, [$config["maxDepth"]]);
    foreach($config["transformers"] as $index => $transformer) {
      $builder->addDefinition($this->prefix("transformer.$index"))
        ->setType($transformer);
    }
  }
}
?>