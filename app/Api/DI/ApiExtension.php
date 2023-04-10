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
      "tokenTtl" => Expect::int(60 * 60),
      "tokenLength" => Expect::int(20),
    ]);
  }

  public function loadConfiguration(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig();
    $builder->addDefinition($this->prefix("entityConverter"))
      ->setFactory(\Nexendrie\Api\EntityConverter::class, [$config->maxDepth]);
    $builder->addDefinition($this->prefix("tokens"))
      ->setFactory(\Nexendrie\Api\Tokens::class, [$config->tokenTtl, $config->tokenLength,]);
    foreach($config->transformers as $index => $transformer) {
      $builder->addDefinition($this->prefix("transformer.$index"))
        ->setType($transformer);
    }
  }
}
?>