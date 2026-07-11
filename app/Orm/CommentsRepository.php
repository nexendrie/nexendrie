<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Comment>
 */
final class CommentsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Comment::class];
    }

    /**
     * @return ICollection<Comment>
     */
    public function findByArticle(Article|int $article): ICollection
    {
        return $this->findBy(["article" => $article, "deleted" => false,]);
    }

    /**
     * @return ICollection<Comment>
     */
    public function findByAuthor(User|int $author): ICollection
    {
        return $this->findBy(["author" => $author, "deleted" => false,]);
    }
}
