<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Article as ArticleEntity;
use Nexendrie\Orm\Punishment as PunishmentEntity;

/**
 * Chronicle Model
 *
 * @author Jakub Konečný
 */
final class Chronicle {
  protected \Nexendrie\Orm\Model $orm;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get list of chronicle records
   *
   * @return ArticleEntity[]|ICollection
   */
  public function articles(\Nette\Utils\Paginator $paginator = null): ICollection {
    $articles = $this->orm->articles->findChronicle();
    if($paginator !== null) {
      //$paginator->itemsPerPage = $this->itemsPerPage;
      $articles = $articles->limitBy($paginator->getLength(), $paginator->getOffset());
    }
    return $articles;
  }
  
  /**
   * Get list of punishments
   * 
   * @return PunishmentEntity[]|ICollection
   */
  public function crimes(): ICollection {
    return $this->orm->punishments->findAll()->orderBy("created", ICollection::DESC);
  }
}
?>