<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

use Nette\Utils\Json;
use Nextras\Orm\Entity\Entity;
use Nette\Utils\Strings;

/**
 * BasePresenter
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nexendrie\Api\EntityConverter */
  protected $entityConverter;
  protected $fields = [];
  protected $fieldsRename = [];
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Api\EntityConverter $entityConverter) {
    parent::__construct();
    $this->orm = $orm;
    $this->entityConverter = $entityConverter;
  }
  
  protected function resourceNotFound(string $resource, int $id): void {
    $this->getHttpResponse()->setCode(404);
    $this->sendJson(["message" => Strings::firstUpper($resource) . " with id $id was not found."]);
  }
  
  protected function methodNotAllowed(): void {
    $method = $this->request->method;
    $this->getHttpResponse()->setCode(405);
    $this->getHttpResponse()->addHeader("Allow", implode(", ", $this->getAllowedMethods()));
    $this->sendJson(["message" => "Method $method is not allowed."]);
  }

  public function actionReadAll(): void {
    $this->methodNotAllowed();
  }

  public function actionRead(): void {
    $this->methodNotAllowed();
  }
  
  public function actionCreate(): void {
    $this->methodNotAllowed();
  }
  
  public function actionUpdate(): void {
    $this->methodNotAllowed();
  }
  
  public function actionPartialUpdate(): void {
    $this->methodNotAllowed();
  }
  
  public function actionDelete(): void {
    $this->methodNotAllowed();
  }

  protected function getCollectionName(): string {
    $presenterName = (string) Strings::before(static::class, "Presenter", -1);
    $presenterName = (string) Strings::after($presenterName, "\\", -1);
    return Strings::firstLower($presenterName);
  }

  protected function sendCollection(iterable $collection, ?string $name = null): void {
    $data = $this->entityConverter->convertCollection($collection);
    $this->sendJson([$name ?? $this->getCollectionName() => $data]);
  }

  protected function getEntityName(): string {
    $name = $this->getCollectionName();
    return substr($name, 0, -1);
  }

  protected function sendEntity(?Entity $entity, ?string $name = null, ?string $name2 = null): void {
    $name = $name ?? $this->getEntityName();
    if(is_null($entity)) {
      $this->resourceNotFound($name2 ?? $name, (int) $this->params["id"]);
    }
    $data = $this->entityConverter->convertEntity($entity);
    $this->sendJson([$name => $data]);
  }

  protected function getPostData(): object {
    $data = ((strlen($this->params["data"]) > 0) ? $this->params["data"] : "{}");
    try {
      return Json::decode($data);
    } catch(\Nette\Utils\JsonException $e) {
      $this->getHttpResponse()->setCode(400);
      $this->sendJson(["message" => "Error while parsing request body: " . $e->getMessage() . "."]);
    }
  }

  /**
   * @return string[]
   */
  protected function getAllowedMethods(): array {
    $methods = [
      "GET" => "actionReadAll", "POST" => "actionCreate", "PUT" => "actionUpdate",
      "PATCH" => "actionPartialUpdate", "DELETE" => "actionDelete",
    ];
    $return = [];
    if(isset($this->params["id"])) {
      $methods["GET"] = "actionRead";
    }
    foreach($methods as $httpMethod => $classMethod) {
      $rm = new \ReflectionMethod(static::class, $classMethod);
      $declaringClass = $rm->getDeclaringClass()->getName();
      if($declaringClass === static::class) {
        $return[] = $httpMethod;
      }
    }
    return $return;
  }
  
  protected function beforeRender(): void {
    $this->getHttpResponse()->setCode(400);
    $this->sendJson(["message" => "This action is not allowed."]);
  }
}
?>