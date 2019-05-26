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
      if(is_null($record)) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->comments;
    } elseif(isset($this->params["associations"]["articles"])) {
      $article = (int) $this->params["associations"]["articles"];
      $record = $this->orm->articles->getById($article);
      if(is_null($record)) {
        $this->resourceNotFound("article", $article);
      }
      $records = $record->comments;
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->comments->findAll();
    }
    $this->sendCollection($records, "comments");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $comment = $this->orm->comments->getById($id);
    $this->sendEntity($comment, "comment");
  }
}
?>