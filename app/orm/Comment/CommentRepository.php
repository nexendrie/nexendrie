<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Comment|NULL getById($id)
 * @method ICollection|Comment findByArticle($article)
 */
class CommentsRepository extends \Nextras\Orm\Repository\Repository {

}
?>