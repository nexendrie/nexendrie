<?php
namespace Nexendrie\Orm;

/**
 * JobDummy
 *
 * @author Jakub Konečný
 */
class JobDummy extends DummyEntity {
  /** @var string */
  protected $name;
  /** @var string */
  protected $description;
  /** @var string */
  protected $help;
  /** @var int */
  protected $count;
  /** @var int */
  protected $award;
  /** @var int */
  protected $shift;
  /** @var int */
  protected $level;
  /** @var int */
  protected $neededSkill;
  /** @var int */
  protected $neededSkillLevel;
  
  function __construct(Job $job) {
    $this->name = $job->name;
    $this->description = $job->description;
    $this->help = $job->help;
    $this->count = $job->count;
    $this->award = $job->award;
    $this->shift = $job->shift;
    $this->level = $job->level;
    $this->neededSkill = $job->neededSkill->id;
    $this->neededSkillLevel = $job->neededSkillLevel;
  }
}
?>