<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * ArticlesPresenter
 *
 * @author Jakub Konečný
 */
final class ArticlesPresenter extends BasePresenter {
  public function actionReadAll(): void {
    if(isset($this->params["associations"]["users"])) {
      $user = (int) $this->params["associations"]["users"];
      $record = $this->orm->users->getById($user);
      if(is_null($record)) {
        $this->resourceNotFound("user", $user);
      }
      $records = $record->articles;
    } elseif(isset($this->params["associations"]) AND count($this->params["associations"]) > 0) {
      return;
    } else {
      $records = $this->orm->articles->findAll();
    }
    $this->sendCollection($records, "articles");
  }
  
  public function actionRead(): void {
    $id = (int) $this->params["id"];
    $article = $this->orm->articles->getById($id);
    $this->sendEntity($article, "article");
  }
}
?>