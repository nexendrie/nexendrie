<?php
declare(strict_types=1);

namespace Nexendrie\Api\DI;

use Nette\Schema\Expect;

/**
 * Api Extension for DIC
 *
 * @author Jakub Konečný
 * @method \stdClass getConfig()
 */
final class ApiExtension extends \Nette\DI\CompilerExtension {
  public function getConfigSchema(): \Nette\Schema\Schema {
    return Expect::structure([
      "transformers" => Expect::arrayOf("class")->default([]),
      "maxDepth" => Expect::int(2),
    ]);
  }

  public function loadConfiguration(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig();
    $builder->addDefinition($this->prefix("entityConverter"))
      ->setFactory(\Nexendrie\Api\EntityConverter::class, [$config->maxDepth]);
    foreach($config->transformers as $index => $transformer) {
      $builder->addDefinition($this->prefix("transformer.$index"))
        ->setType($transformer);
    }
  }
}
?>