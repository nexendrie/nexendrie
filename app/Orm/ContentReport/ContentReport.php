<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ContentReport
 *
 * @property int $id {primary}
 * @property Comment $comment {m:1 Comment, oneSided=true}
 * @property User $user {m:1 User, oneSided=true}
 * @property bool $handled {default false}
 * @property int $created
 */
final class ContentReport extends BaseEntity {

}
?>