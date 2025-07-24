<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Model as ORM;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Article as ArticleEntity;
use Nexendrie\Orm\Punishment as PunishmentEntity;

/**
 * Chronicle Model
 *
 * @author Jakub Konečný
 */
final class Chronicle {
  public function __construct(private readonly ORM $orm) {
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
  public function crimes(string $user = ""): ICollection {
    if($user === "") {
      $collection = $this->orm->punishments->findAll();
    } else {
      $collection = $this->orm->punishments->findByUserPublicname($user);
    }
    return $collection->orderBy("created", ICollection::DESC);
  }
}
?>