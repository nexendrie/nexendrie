<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * JobMessage
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property Job $job {m:1 Job::$messages}
 * @property bool $success
 * @property string $message
 * @property int $created
 */
final class JobMessage extends BaseEntity {
  
}
?>