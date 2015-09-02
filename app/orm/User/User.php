<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;


/**
 * User
 *
 * @author Jakub Konečný
 * @property string $username
 * @property string $publicname
 * @property string $password
 * @property string $email
 * @property int $joined
 * @property int $lastActive
 * @property Group $group {m:1 Group::$members}
 * @property int $infomails
 * @property string $style
 * @property int $banned
 * @property int $money
 * @property OneHasMany|Comment[] $comments {1:m Comment::$author}
 */
class User extends Entity {

}
?>