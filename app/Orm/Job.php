<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Job
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property string $help
 * @property int $count {default 0}
 * @property int $award
 * @property int $shift
 * @property int $level {default 50}
 * @property Skill $neededSkill {m:1 Skill::$jobs}
 * @property int $neededSkillLevel {default 0}
 * @property int $created
 * @property int $updated
 * @property OneHasMany|UserJob[] $userJobs {1:m UserJob::$job}
 * @property OneHasMany|JobMessage[] $messages {1:m JobMessage::$job}
 */
final class Job extends BaseEntity {

}
?>