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
 * @property int $created
 * @property bool $finished {default false}
 * @property int|null $lastAction {default null}
 * @property int $count {default 0}
 * @property int $earned {default 0}
 * @property int $extra {default 0}
 * @property-read int $finishTime {virtual}
 * @property-read int[] $reward {virtual}
 * @property-read int $successRate {virtual}
 */
final class UserJob extends BaseEntity {
  /** Base success rate for job (in %) */
  public const BASE_SUCCESS_RATE = 55;
  public const JOB_DAYS_LENGTH = 7;
  
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  
  public function injectEventsModel(\Nexendrie\Model\Events $eventsModel): void {
    $this->eventsModel = $eventsModel;
  }
  
  protected function getterFinishTime(): int {
    return $this->created + (60 * 60 * 24 * static::JOB_DAYS_LENGTH);
  }
  
  protected function getHouseRewardBonus(int $baseReward): int {
    if($this->user->house !== null) {
      return (int) ($baseReward / 100 * $this->user->house->workIncomeBonus);
    }
    return 0;
  }
  
  protected function getGuildRewardBonus(int $baseReward): int {
    if($this->user->guild && $this->user->group->path === Group::PATH_CITY) {
      if($this->job->neededSkill->id === $this->user->guild->skill->id) {
        $increase = $this->user->guildRank->incomeBonus + $this->user->guild->jobBonusIncome;
        return (int) ($baseReward / 100 * $increase);
      }
    }
    return 0;
  }
  
  protected function getSkillRewardBonus(int $baseReward): int {
    /** @var UserSkill|null $userSkill */
    $userSkill = $this->user->skills->get()->getBy([
      "skill" => $this->job->neededSkill->id
    ]);
    if($userSkill === null) {
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
    /** @var UserSkill|null $userSkill */
    $userSkill = $this->user->skills->get()->getBy([
      "skill" => $this->job->neededSkill->id
    ]);
    if($userSkill === null) {
      return 0;
    }
    return $userSkill->jobSuccessRateBonus;
  }
  
  protected function getterSuccessRate(): int {
    $successRate = static::BASE_SUCCESS_RATE;
    $successRate += $this->getSkillSuccessRateBonus();
    return $successRate;
  }
}
?>