<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ICollection|Article[] findByLikeTitle(string $title)
 * @method ICollection|Article[] findByText(string $text)
 */
final class ArticlesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Article::class];
  }
  
  /**
   * @param int $id
   * @return Article|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @return ICollection|Article[]
   */
  public function findByCategory(string $category): ICollection {
    return $this->findBy(["category" => $category]);
  }
  
  /**
   * @return ICollection|Article[]
   */
  public function findNews(): ICollection {
    return $this->findBy(["category" => Article::CATEGORY_NEWS])->orderBy("added", ICollection::DESC);
  }
  
  /**
   * @return ICollection|Article[]
   */
  public function findChronicle(): ICollection {
    return $this->findBy(["category" => Article::CATEGORY_CHRONICLE])->orderBy("added", ICollection::DESC);
  }
}
?>