<?php
declare(strict_types=1);

namespace Nexendrie\Api\DI;

use Nette\Schema\Expect;
use Nexendrie\Api\Transformers\ITransformer;

/**
 * Api Extension for DIC
 *
 * @author Jakub Konečný
 * @method \stdClass getConfig()
 */
final class ApiExtension extends \Nette\DI\CompilerExtension {
  public function getConfigSchema(): \Nette\Schema\Schema {
    return Expect::structure([
      "transformersNamespace" => Expect::string()->required(),
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
      ->setFactory(\Nexendrie\Api\Tokens::class, [$config->tokenTtl, $config->tokenLength, ]);
    /** @var class-string[] $transformers */
    $transformers = array_keys(require __DIR__ . "/../../../vendor/composer/autoload_classmap.php");
    $transformers = array_values(array_filter($transformers, function (string $transformer) use ($config) {
      if(!str_starts_with($transformer, $config->transformersNamespace . "\\")) {
        return false;
      }
      $rc = new \ReflectionClass($transformer);
      return $rc->isInstantiable() && $rc->implementsInterface(ITransformer::class);
    }));
    foreach($transformers as $index => $transformer) {
      $builder->addDefinition($this->prefix("transformer.$index"))
        ->setType($transformer);
    }
  }
}
?>