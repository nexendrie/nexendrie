<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * CommentsPresenter
 *
 * @author Jakub Konečný
 */
final class CommentsPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      $record = $this->orm->users->getById($user);
      if($record === null) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->comments;
    } elseif(isset($this->params["associations"]["articles"])) {
      $article = (int) $this->params["associations"]["articles"];
      $record = $this->orm->articles->getById($article);
      if($record === null) {
        $this->resourceNotFound("article", $article);
      }
      $records = $record->comments;
    } elseif(isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->comments->findAll();
    }
    $this->sendCollection($records);
  }
  
  public function actionRead(): void {
    $record = $this->orm->comments->getById($this->getId());
    $this->sendEntity($record);
  }
}
?>