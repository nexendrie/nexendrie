<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * News
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $text
 * @property User $author {m:1 User}
 * @property int $added
 * @property bool $allowedComments {default 1}
 * @property OneHasMany|Comment $comments {1:m Comment::$news}
 */
class News extends Entity {
  
}
?>