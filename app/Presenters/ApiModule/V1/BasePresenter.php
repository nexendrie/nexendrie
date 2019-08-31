<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

use Nette\Application\Responses\TextResponse;
use Nette\Http\IResponse;
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
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Api\EntityConverter $entityConverter) {
    parent::__construct();
    $this->orm = $orm;
    $this->entityConverter = $entityConverter;
  }

  /**
   * If no response was sent, we received a request that we are not able to handle.
   * That means e. g. invalid associations.
   */
  protected function beforeRender(): void {
    $this->getHttpResponse()->setCode(IResponse::S400_BAD_REQUEST);
    $this->sendJson(["message" => "This action is not allowed."]);
  }

  protected function shutdown($response) {
    parent::shutdown($response);
    // do not send cookies with response, they are not (meant to be) used for authentication
    $this->getHttpResponse()->setHeader("Set-Cookie", null);
  }

  /**
   * @return string[]
   */
  protected function getAllowedMethods(): array {
    // we check in which class a method was defined to decide if the corresponding HTTP method is allowed
    // this base presenter forbids all methods, so if a subclass overrides it, we assume that it is allowed
    $methods = [
      "GET" => "actionReadAll", "POST" => "actionCreate", "PUT" => "actionUpdate",
      "PATCH" => "actionPartialUpdate", "DELETE" => "actionDelete",
    ];
    $return = ["OPTIONS",];
    if(isset($this->params["id"])) {
      $methods["GET"] = "actionRead";
    }
    $methods["HEAD"] = $methods["GET"];
    foreach($methods as $httpMethod => $classMethod) {
      $rm = new \ReflectionMethod(static::class, $classMethod);
      $declaringClass = $rm->getDeclaringClass()->getName();
      if($declaringClass === static::class) {
        $return[] = $httpMethod;
      }
    }
    return $return;
  }

  /**
   * A quick way to send 404 status with an appropriate message to the client.
   * It is meant to be used only in @see sendEntity method
   * or @see actionReadAll method when the associated resource was not found.
   */
  protected function resourceNotFound(string $resource, int $id): void {
    $this->getHttpResponse()->setCode(IResponse::S404_NOT_FOUND);
    $this->sendJson(["message" => Strings::firstUpper($resource) . " with id $id was not found."]);
  }

  /**
   * A quick way to send 405 status with an appropriate message and list of allowed methods to the client.
   * It is send by default to all HTTP methods but OPTIONS.
   */
  protected function methodNotAllowed(): void {
    $method = $this->request->method;
    $this->getHttpResponse()->setCode(IResponse::S405_METHOD_NOT_ALLOWED);
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

  public function actionOptions(): void {
    $this->getHttpResponse()->setCode(IResponse::S204_NO_CONTENT);
    $this->getHttpResponse()->addHeader("Allow", implode(", ", $this->getAllowedMethods()));
    $this->sendResponse(new TextResponse(""));
  }

  protected function getCollectionName(): string {
    $presenterName = (string) Strings::before(static::class, "Presenter", -1);
    $presenterName = (string) Strings::after($presenterName, "\\", -1);
    return Strings::firstLower($presenterName);
  }

  /**
   * A quick way to send a collection of entities as response.
   * It is meant to be used in @see actionReadAll method.
   */
  protected function sendCollection(iterable $collection, ?string $name = null): void {
    $data = $this->entityConverter->convertCollection($collection);
    $this->sendJson([$name ?? $this->getCollectionName() => $data]);
  }

  protected function getEntityName(): string {
    $name = $this->getCollectionName();
    return substr($name, 0, -1);
  }

  /**
   * A quick way to send single entity as response.
   * It is meant to be used in @see actionRead method.
   */
  protected function sendEntity(?Entity $entity, ?string $name = null, ?string $name2 = null): void {
    $name = $name ?? $this->getEntityName();
    if(is_null($entity)) {
      $this->resourceNotFound($name2 ?? $name, $this->getId());
    }
    $data = $this->entityConverter->convertEntity($entity);
    $links = $this->getEntityLinks();
    foreach($links as $rel => $link) {
      $this->getHttpResponse()->addHeader("Link", "<$link>; rel=\"$rel\"");
    }
    $this->sendJson([$name => $data]);
  }

  protected function getPostData(): object {
    $data = ((strlen($this->params["data"]) > 0) ? $this->params["data"] : "{}");
    try {
      return Json::decode($data);
    } catch(\Nette\Utils\JsonException $e) {
      $this->getHttpResponse()->setCode(IResponse::S400_BAD_REQUEST);
      $this->sendJson(["message" => "Error while parsing request body: " . $e->getMessage() . "."]);
    }
  }

  protected function getId(): int {
    return (int) $this->params["id"];
  }

  protected function createEntityLink(string $targetCollectionName): string {
    $targetCollectionName = Strings::firstUpper($targetCollectionName);
    $params = ["associations" => [$this->getCollectionName() => $this->getId()]];
    return $this->link("$targetCollectionName:readAll", $params);
  }

  /**
   * Defines links of current entity to (collections of) other entities. Used by @see sendEntity.
   * When overriding, store result of parent method and add new elements of array.
   * Key is type of relationship, value is the link. The link should start with /.
   * Use @see createEntityLink to get urls.
   *
   * @return string[]
   */
  protected function getEntityLinks(): array {
    return [
      "self" => $this->getHttpRequest()->getUrl()->path,
    ];
  }
}
?>