<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Comment|NULL getById($id)
 * @method ICollection|Comment findByNews($news)
 */
class CommentsRepository extends Repository {

}
?>