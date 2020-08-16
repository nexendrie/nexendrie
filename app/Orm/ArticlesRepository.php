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
   */
  public function getById($id): ?Article {
    return $this->getBy(["id" => $id]);
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