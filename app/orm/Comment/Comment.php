<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;


/**
 * Comment
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $text
 * @property News $news {m:1 News::$comments}
 * @property User $author {m:1 User::$comments}
 * @property int $added
 */
class Comment extends Entity {

}
?>