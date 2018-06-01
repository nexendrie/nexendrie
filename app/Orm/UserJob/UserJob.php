<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * UserJob
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$jobs}
 * @property Job $job {m:1 Job::$userJobs}
 * @property int $started
 * @property bool $finished {default false}
 * @property int|NULL $lastAction {default NULL}
 * @property int $count {default 0}
 * @property int $earned {default 0}
 * @property int $extra {default 0}
 * @property-read int $finishTime {virtual}
 * @property-read int[] $reward {virtual}
 * @property-read int $successRate {virtual}
 */
final class UserJob extends \Nextras\Orm\Entity\Entity {
  /** Base success rate for job (in %) */
  public const BASE_SUCCESS_RATE = 55;
  
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  
  public function injectEventsModel(\Nexendrie\Model\Events $eventsModel) {
    $this->eventsModel = $eventsModel;
  }
  
  protected function getterFinishTime(): int {
    return $this->started + (60 * 60 * 24 * 7);
  }
  
  protected function getHouseRewardBonus(int $baseReward): int {
    if(!is_null($this->user->house)) {
      return (int) ($baseReward / 100 * $this->user->house->workIncomeBonus);
    }
    return 0;
  }
  
  protected function getGuildRewardBonus(int $baseReward): int {
    if($this->user->guild AND $this->user->group->path === Group::PATH_CITY) {
      if($this->job->neededSkill->id === $this->user->guild->skill->id) {
        $increase = $this->user->guildRank->incomeBonus + $this->user->guild->level - 1;
        return (int) ($baseReward /100 * $increase);
      }
    }
    return 0;
  }
  
  protected function getSkillRewardBonus(int $baseReward): int {
    /** @var UserSkill|NULL $userSkill */
    $userSkill = $this->user->skills->get()->getBy([
      "id" => $this->job->neededSkill->id
    ]);
    if(is_null($userSkill)) {
      return 0;
    }
    return (int) ($baseReward / 100 * $userSkill->jobRewardBonus);
  }
  
  /**
   * @return int[]
   */
  protected function getterReward(): array {
    if($this->finished) {
      return ["reward" => $this->earned, "extra" => $this->extra];
    }
    $reward = $extra = 0;
    if($this->job->count === 0) {
      $reward += $this->job->award * $this->count;
    } elseif($this->count >= $this->job->count) {
      $reward += $this->job->award;
      if($this->count >= $this->job->count * 1.2) {
        $extra += (int) ($this->job->award / 5);
      }
      if($this->count >= $this->job->count * 1.5) {
        $extra += (int) ($this->job->award / 2);
      }
    }
    $extra += $this->eventsModel->calculateWorkBonus($reward);
    $extra += $this->getHouseRewardBonus($reward);
    $extra += $this->getGuildRewardBonus($reward);
    $extra += $this->getSkillRewardBonus($reward);
    return ["reward" => (int) round($reward), "extra" => (int) round($extra)];
  }
  
  protected function getSkillSuccessRateBonus(): int {
    /** @var UserSkill|NULL $userSkill */
    $userSkill = $this->user->skills->get()->getBy([
      "id" => $this->job->neededSkill->id
    ]);
    if(is_null($userSkill)) {
      return 0;
    }
    return $userSkill->jobSuccessRateBonus;
  }
  
  protected function getterSuccessRate(): int {
    $successRate = static::BASE_SUCCESS_RATE;
    $successRate += $this->getSkillSuccessRateBonus();
    return $successRate;
  }
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->started = time();
  }
}
?>