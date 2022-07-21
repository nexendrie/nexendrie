<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Article|null getById(int $id)
 * @method Article|null getBy(array $conds)
 * @method ICollection|Article[] findBy(array $conds)
 * @method ICollection|Article[] findAll()
 * @method ICollection|Article[] findByText(string $text)
 */
final class ArticlesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Article::class];
  }
  
  /**
   * @return ICollection|Article[]
   */
  public function findByCategory(string $category): ICollection {
    return $this->findBy(["category" => $category]);
  }
  
  /**
   * @param User|int $author
   * @return ICollection|Article[]
   */
  public function findByAuthor($author): ICollection {
    return $this->findBy(["author" => $author]);
  }
  
  /**
   * @return ICollection|Article[]
   */
  public function findNews(): ICollection {
    return $this->findBy(["category" => Article::CATEGORY_NEWS])->orderBy("created", ICollection::DESC);
  }
  
  /**
   * @return ICollection|Article[]
   */
  public function findChronicle(): ICollection {
    return $this->findBy(["category" => Article::CATEGORY_CHRONICLE])->orderBy("created", ICollection::DESC);
  }
}
?>