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
 * @property int|NULL $lastActive
 * @property Group $group {m:1 Group::$members}
 * @property int $infomails {default 1}
 * @property string $style {default default}
 * @property int $banned {default 0}
 * @property int $money {default 2}
 * @property OneHasMany|Comment[] $comments {1:m Comment::$author}
 */
class User extends Entity {

}
?>