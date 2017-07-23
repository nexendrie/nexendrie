<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Article as ArticleEntity,
    Nexendrie\Orm\Punishment as PunishmentEntity;

/**
 * Chronicle Model
 *
 * @author Jakub Konečný
 */
class Chronicle {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get list of chronicle records
   *
   * @return ArticleEntity[]|ICollection
   */
  public function articles(\Nette\Utils\Paginator $paginator = NULL): ICollection {
    $articles = $this->orm->articles->findChronicle();
    if(!is_null($paginator)) {
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
    return $this->orm->punishments->findAll()->orderBy("imprisoned", ICollection::DESC);
  }
}
?>