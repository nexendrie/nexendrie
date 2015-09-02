<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;


/**
 * Comment
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $text
 * @property int $news
 * @property User $author {m:1 User::$comments}
 * @property int $added
 */
class Comment extends Entity {

}
?>