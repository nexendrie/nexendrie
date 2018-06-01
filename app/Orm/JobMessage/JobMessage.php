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
 */
final class JobMessage extends \Nextras\Orm\Entity\Entity {
  
}
?>