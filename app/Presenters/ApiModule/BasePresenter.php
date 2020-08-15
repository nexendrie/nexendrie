<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule;

use Nette\Application\Responses\TextResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Nextras\Orm\Entity\Entity;

/**
 * BasePresenter
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  protected \Nexendrie\Orm\Model $orm;
  protected \Nexendrie\Api\EntityConverter $entityConverter;
  protected bool $cachingEnabled = true;
  protected bool $publicCache = true;

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
    $this->getHttpResponse()->deleteHeader("Set-Cookie");
  }

  /**
   * @return string[]
   */
  protected function getAllowedMethods(): array {
    // we check in which class a method was defined to decide if the corresponding HTTP method is allowed
    // this base presenter forbids all methods, so if a subclass overrides it, we assume that it is allowed
    $methods = [
      IRequest::GET => "actionReadAll", IRequest::POST => "actionCreate", IRequest::PUT => "actionUpdate",
      IRequest::PATCH => "actionPartialUpdate", IRequest::DELETE => "actionDelete",
    ];
    $return = [IRequest::OPTIONS, ];
    if(isset($this->params["id"])) {
      $methods[IRequest::GET] = "actionRead";
    }
    $methods[IRequest::HEAD] = $methods[IRequest::GET];
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
    $payload = ["message" => "Method $method is not allowed."];
    $this->addContentLengthHeader($payload);
    $this->sendJson($payload);
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

  abstract protected function getApiVersion(): string;

  /**
   * @param string|int|\DateTimeInterface $lastModified
   */
  public function lastModified($lastModified, string $etag = null, string $expire = null): void {
    $this->getHttpResponse()->deleteHeader("Pragma");
    $this->getHttpResponse()->deleteHeader("Vary");
    if(!$this->cachingEnabled) {
      return;
    }
    $this->getHttpResponse()->setHeader("Cache-Control", ($this->publicCache) ? "public" : "private");
    parent::lastModified($lastModified, $etag, $expire);
  }

  protected function getCollectionName(): string {
    $presenterName = (string) Strings::before(static::class, "Presenter", -1);
    $presenterName = (string) Strings::after($presenterName, "\\", -1);
    return Strings::firstLower($presenterName);
  }

  protected function getCollectionModifiedTime(iterable $collection): int {
    $time = 0;
    foreach($collection as $entity) {
      if(isset($entity->updated)) {
        $time = max($time, $entity->updated);
      }
    }
    return $time;
  }

  /**
   * A quick way to send a collection of entities as response.
   * It is meant to be used in @see actionReadAll method.
   */
  protected function sendCollection(iterable $collection): void {
    $data = $this->entityConverter->convertCollection($collection, $this->getApiVersion());
    $this->getHttpResponse()->addHeader("Link", $this->createLinkHeader("self", $this->getSelfLink()));
    $payload = [$this->getCollectionName() => $data];
    $this->lastModified($this->getCollectionModifiedTime($collection));
    $this->addContentLengthHeader($payload);
    $this->sendJson($payload);
  }

  protected function getEntityName(): string {
    $name = $this->getCollectionName();
    return substr($name, 0, -1);
  }

  protected function getInvalidEntityName(): string {
    $name = $this->getEntityName();
    return preg_replace_callback("#[A-Z]#", function(array $letter) {
      return " " . Strings::lower($letter[0]);
    }, $name);
  }

  protected function getEntityModifiedTime(Entity $entity): int {
    $time = 0;
    if(isset($entity->updated)) {
      $time = max($time, $entity->updated);
    }
    return $time;
  }

  /**
   * A quick way to send single entity as response.
   * It is meant to be used in @see actionRead method.
   */
  protected function sendEntity(?Entity $entity): void {
    if($entity === null) {
      $this->resourceNotFound($this->getInvalidEntityName(), $this->getId());
    }
    $data = $this->entityConverter->convertEntity($entity, $this->getApiVersion());
    $links = $data->_links ?? [];
    foreach($links as $rel => $link) {
      $this->getHttpResponse()->addHeader("Link", $this->createLinkHeader($link->rel, $link->href));
    }
    $payload = [$this->getEntityName() => $data];
    $this->lastModified($this->getEntityModifiedTime($entity));
    $this->addContentLengthHeader($payload);
    $this->sendJson($payload);
  }

  protected function addContentLengthHeader(array $payload): void {
    $this->getHttpResponse()->addHeader("Content-Length", (string) strlen(Json::encode($payload)));
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

  protected function createLinkHeader(string $rel, string $link): string {
    return "<$link>; rel=\"$rel\"";
  }

  protected function getSelfLink(): string {
    $url = $this->getHttpRequest()->getUrl();
    return $url->hostUrl . $url->path;
  }
}
?>