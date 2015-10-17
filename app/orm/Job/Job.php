<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Job
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property string $help
 * @property int $count {default 0}
 * @property int $award
 * @property-read string $awardT {virtual}
 * @property int $shift
 * @property int $level {default 50}
 * @property Skill $neededSkill {m:1 Skill::$jobs}
 * @property int $neededSkillLevel {default 0}
 * @property OneHasMany|UserJob[] $userJobs {1:m UserJob::$job}
 * @property OneHasMany|JobMessage[] $messages {1:m JobMessage::$job}
 */
class Job extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterAwardT() {
    return $this->localeModel->money($this->award);
  }
  
  /**
   * @return JobDummy
   */
  function dummy() {
    return new JobDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>