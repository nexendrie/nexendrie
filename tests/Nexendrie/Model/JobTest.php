<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Orm\Job as JobEntity;
use Nexendrie\Orm\UserJob as UserJobEntity;
use Nexendrie\Orm\JobMessage;

require __DIR__ . "/../../bootstrap.php";

final class JobTest extends \Tester\TestCase {
  use TUserControl;

  protected Job $model;
  protected \Nexendrie\Orm\Model $orm;
  
  protected function setUp(): void {
    $this->model = $this->getService(Job::class); // @phpstan-ignore assign.propertyType
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testListOfJobs(): void {
    $result = $this->model->listOfJobs();
    Assert::type(ICollection::class, $result);
    Assert::type(JobEntity::class, $result->fetch());
  }
  
  public function testCalculateAward(): void {
    Assert::exception(function() {
      $this->model->calculateAward(new JobEntity());
    }, AuthenticationNeededException::class);
    $job = $this->model->getJob(1);
    $this->login();
    $result = $this->model->calculateAward($job);
    Assert::type(\stdClass::class, $result);
    Assert::type("int", $result->award);
  }
  
  public function testFindAvailableJobs(): void {
    Assert::exception(function() {
      $this->model->findAvailableJobs();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findAvailableJobs();
    Assert::type("array", $result);
    Assert::type(\stdClass::class, $result[0]);
  }
  
  public function testGetJob(): void {
    $job = $this->model->getJob(1);
    Assert::type(JobEntity::class, $job);
    Assert::exception(function() {
      $this->model->getJob(50);
    }, JobNotFoundException::class);
  }
  
  public function testEditJob(): void {
    Assert::exception(function() {
      $this->model->editJob(50, []);
    }, JobNotFoundException::class);
    $job = $this->model->getJob(1);
    $name = $job->name;
    $this->model->editJob($job->id, ["name" => "abc"]);
    Assert::same("abc", $job->name);
    $this->model->editJob($job->id, ["name" => $name]);
  }
  
  public function testStartJob(): void {
    $this->login("bozena");
    Assert::exception(function() {
      $this->model->startJob(5000);
    }, JobNotFoundException::class);
    Assert::exception(function() {
      $this->model->startJob(4);
    }, InsufficientLevelForJobException::class);
    $this->login("premysl");
    Assert::exception(function() {
      $this->model->startJob(4);
    }, InsufficientSkillLevelForJobException::class);
  }
  
  protected function checkReward(array $reward, int $base, int $extra): void {
    Assert::count(2, $reward);
    Assert::same($base, $reward["reward"]);
    Assert::same($extra, $reward["extra"]);
  }
  
  public function testGetResultMessage(): void {
    $this->login();
    $message = $this->model->getResultMessage(1, true);
    Assert::type("string", $message);
    Assert::notSame("Úspěšně jsi zvládl směnu.", $message);
    $message = $this->model->getResultMessage(1, false);
    Assert::type("string", $message);
    Assert::notSame("Nezvládl jsi tuto směnu.", $message);
    $message = $this->model->getResultMessage(8, true);
    Assert::type("string", $message);
    Assert::same("Úspěšně jsi zvládl směnu.", $message);
    $message = $this->model->getResultMessage(8, false);
    Assert::type("string", $message);
    Assert::same("Nezvládl jsi tuto směnu.", $message);
  }
  
  public function testIsWorking(): void {
    Assert::exception(function() {
      $this->model->isWorking();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->isWorking());
  }
  
  public function testGetCurrentJob(): void {
    Assert::exception(function() {
      $this->model->getCurrentJob();
    }, AuthenticationNeededException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->getCurrentJob();
    }, NotWorkingException::class);
  }
  
  public function testParseJobHelp(): void {
    $this->login("bozena");
    $job = new UserJobEntity();
    $job->user = $this->getUser();
    $job->job = $this->orm->jobs->getById(3); // @phpstan-ignore assign.propertyType
    $result = $this->model->parseJobHelp($job);
    Assert::same("Postarej se o tohle zvíře na 2 hodiny. Pokud se alespoň 13 nic nestane, dostaneš 70 grošů.", $result);
  }
  
  public function testCanWork(): void {
    Assert::exception(function() {
      $this->model->canWork();
    }, AuthenticationNeededException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->canWork();
    }, NotWorkingException::class);
  }
  
  public function testListOfMessages(): void {
    $result = $this->model->listOfMessages(1);
    Assert::type(OneHasMany::class, $result);
    Assert::type(JobMessage::class, $result->getIterator()->fetch());
    Assert::exception(function() {
      $this->model->listOfMessages(5000);
    }, JobNotFoundException::class);
  }
  
  public function testGetMessage(): void {
    $message = $this->model->getMessage(1);
    Assert::type(JobMessage::class, $message);
    Assert::exception(function() {
      $this->model->getMessage(5000);
    }, JobMessageNotFoundException::class);
  }
  
  public function testEditMessage(): void {
    Assert::exception(function() {
      $this->model->editMessage(5000, []);
    }, JobMessageNotFoundException::class);
    $message = $this->model->getMessage(1);
    $success = $message->success;
    $this->model->editMessage(1, ["success" => 1]);
    Assert::true($message->success);
    $this->model->editMessage(1, ["success" => $success]);
  }
  
  public function testDeleteJob(): void {
    Assert::exception(function() {
      $this->model->deleteMessage(5000);
    }, JobMessageNotFoundException::class);
  }
}

$test = new JobTest();
$test->run();
?>