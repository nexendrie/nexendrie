<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

use Nette\Http\IResponse;
use Nette\Security\AuthenticationException;
use Nexendrie\Api\ApiNotEnabledException;

/**
 * TokensPresenter
 *
 * @author Jakub Konečný
 */
final class TokensPresenter extends BasePresenter {
  protected bool $publicCache = false;

  public function actionReadAll(): void {
    $this->requiresLogin();
    if(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    }
    $records = $this->orm->apiTokens->findActiveForUser($this->user->id);
    $this->sendCollection($records);
  }

  public function actionRead(): void {
    $this->requiresLogin();
    $record = $this->orm->apiTokens->getById($this->getId());
    if($record !== null) {
      if($record->user->id !== $this->user->id || $record->expire <= time()) {
        $record = null;
      }
    }
    $this->sendEntity($record);
  }

  public function actionCreate(): void {
    try {
      $credentials = $this->getBasicCredentials();
      $identity = $this->authenticator->authenticate($credentials[0], $credentials[1]);
      $this->user->login($identity);
    } catch(AuthenticationException $e) {
      $this->sendBasicAuthRequest($e->getMessage());
    } catch(\Throwable $e) {
      $this->sendBasicAuthRequest();
    }
    try {
      $token = $this->tokens->create();
    } catch(ApiNotEnabledException $e) {
      $this->getHttpResponse()->setCode(IResponse::S403_FORBIDDEN);
      $this->sendJson(["message" => "You do not have API enabled."]);
    }
    $this->sendCreatedEntity($token);
  }

  public function actionDelete(): void {
    $this->requiresLogin();
    $record = $this->orm->apiTokens->getById($this->getId());
    if($record !== null) {
      if($record->user->id !== $this->user->id || $record->expire <= time()) {
        $record = null;
      }
    }
    if($record !== null) {
      $this->tokens->invalidate($record->token);
    }
    $this->sendDeletedEntity($record);
  }
}
?>