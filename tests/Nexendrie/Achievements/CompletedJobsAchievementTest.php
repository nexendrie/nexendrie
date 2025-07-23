<?php
declare(strict_types=1);

namespace Nexendrie\Achievements;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;
use Nexendrie\Orm\UserJob;
use Nexendrie\Orm\User;

final class CompletedJobsAchievementTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  protected CompletedJobsAchievement $model;
  protected \Nexendrie\Orm\Model $orm;
  
  protected function setUp(): void {
    $this->model = $this->getService(CompletedJobsAchievement::class); // @phpstan-ignore assign.propertyType
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
  }
  
  protected function generateJob(User $user): UserJob {
    $job = new UserJob();
    $this->orm->userJobs->attach($job);
    $job->user = $user;
    $job->job = 1;
    $job->created = time();
    $job->finished = true;
    $job->earned = 1;
    return $job;
  }
  
  public function testGetName(): void {
    Assert::same("Pracant", $this->model->getName());
  }
  
  public function testIsAchievedAndGetProgress(): void {
    /** @var User $user */
    $user = $this->orm->users->getById(1);
    Assert::same(0, $this->model->getProgress($user));
    Assert::same(0, $this->model->isAchieved($user));
    $jobs = [];
    $jobs[] = $this->generateJob($user);
    $this->orm->userJobs->persistAndFlush($jobs[0]);
    Assert::same(1, $this->model->getProgress($user));
    Assert::same(1, $this->model->isAchieved($user));
    $finalCount = $this->model->getRequirements()[3];
    for($i = 2; $i <= $finalCount; $i++) {
      $jobs[] = $job = $this->generateJob($user);
      $this->orm->userJobs->persist($job);
    }
    $this->orm->userJobs->flush();
    Assert::same($finalCount, $this->model->getProgress($user));
    Assert::same(4, $this->model->isAchieved($user));
    foreach($jobs as $job) {
      $this->orm->userJobs->remove($job);
    }
    $this->orm->userJobs->flush();
  }
}

$test = new CompletedJobsAchievementTest();
$test->run();
?>