<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nextras\Orm\Relationships\OneHasMany,
    Nexendrie\Orm\Job as JobEntity,
    Nexendrie\Orm\UserJob as UserJobEntity,
    Nexendrie\Orm\JobMessage;

require __DIR__ . "/../../bootstrap.php";

final class JobTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Job */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function setUp() {
    $this->model = $this->getService(Job::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  public function testListOfJobs() {
    $result = $this->model->listOfJobs();
    Assert::type(ICollection::class, $result);
    Assert::type(JobEntity::class, $result->fetch());
  }
  
  public function testCalculateAward() {
    Assert::exception(function() {
      $this->model->calculateAward(new JobEntity);
    }, AuthenticationNeededException::class);
    $job = $this->model->getJob(1);
    $this->login();
    $result = $this->model->calculateAward($job);
    Assert::type(\stdClass::class, $result);
    Assert::type("string", $result->award);
    Assert::contains("groš", $result->award);
  }
  
  public function testFindAvailableJobs() {
    Assert::exception(function() {
      $this->model->findAvailableJobs();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findAvailableJobs();
    Assert::type("array", $result);
    Assert::type(\stdClass::class, $result[0]);
  }
  
  public function testGetJob() {
    $job = $this->model->getJob(1);
    Assert::type(JobEntity::class, $job);
    Assert::exception(function() {
      $this->model->getJob(50);
    }, JobNotFoundException::class);
  }
  
  public function testEditJob() {
    Assert::exception(function() {
      $this->model->editJob(50, []);
    }, JobNotFoundException::class);
    $job = $this->model->getJob(1);
    $name = $job->name;
    $this->model->editJob($job->id, ["name" => "abc"]);
    Assert::same("abc", $job->name);
    $this->model->editJob($job->id, ["name" => $name]);
  }
  
  public function testStartJob() {
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
  
  public function testCalculateReward() {
    $job = new UserJobEntity;
    $job->finished = true;
    $job->earned = 2;
    $job->extra = 1;
    $this->checkReward($this->model->calculateReward($job), 2, 1);
    $this->login("bozena");
    $job->user = $this->getUser();
    $job->finished = false;
    $job->earned = $job->extra = 0;
    $job->job = $this->orm->jobs->getById(1);
    $job->count = 2;
    $this->checkReward($this->model->calculateReward($job), 2 * 2, 0);
    $job->job = $this->orm->jobs->getById(2);
    $this->checkReward($this->model->calculateReward($job), 0, 0);
    $job->count = 20;
    $this->checkReward($this->model->calculateReward($job), 80, 0);
    $job->count = 25;
    $this->checkReward($this->model->calculateReward($job), 80, 16);
    $job->count = 31;
    $this->checkReward($this->model->calculateReward($job), 80, 56);
  }
  
  public function testGetResultMessage() {
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
  
  public function testIsWorking() {
    Assert::exception(function() {
      $this->model->isWorking();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->isWorking());
  }
  
  public function testGetCurrentJob() {
    Assert::exception(function() {
      $this->model->getCurrentJob();
    }, AuthenticationNeededException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->getCurrentJob();
    }, NotWorkingException::class);
  }
  
  public function testParseJobHelp() {
    $this->login("bozena");
    $job = new UserJobEntity;
    $job->user = $this->getUser();
    $job->job = $this->orm->jobs->getById(3);
    $result = $this->model->parseJobHelp($job);
    Assert::same("Postarej se o tohle zvíře na 2 hodiny. Pokud se alespoň 13 nic nestane, dostaneš 70 grošů.", $result);
  }
  
  public function testCanWork() {
    Assert::exception(function() {
      $this->model->canWork();
    }, AuthenticationNeededException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->canWork();
    }, NotWorkingException::class);
  }
  
  public function testListOfMessages() {
    $result = $this->model->listOfMessages(1);
    Assert::type(OneHasMany::class, $result);
    Assert::type(JobMessage::class, $result->get()->fetch());
    Assert::exception(function() {
      $this->model->listOfMessages(50);
    }, JobNotFoundException::class);
  }
  
  public function testGetMessage() {
    $message = $this->model->getMessage(1);
    Assert::type(JobMessage::class, $message);
    Assert::exception(function() {
      $this->model->getMessage(50);
    }, JobMessageNotFoundException::class);
  }
  
  public function testEditMessage() {
    Assert::exception(function() {
      $this->model->editMessage(5000, []);
    }, JobMessageNotFoundException::class);
    $message = $this->model->getMessage(1);
    $success = $message->success;
    $this->model->editMessage(1, ["success" => 1]);
    Assert::true($message->success);
    $this->model->editMessage(1, ["success" => $success]);
  }
  
  public function testDeleteJob() {
    Assert::exception(function() {
      $this->model->deleteMessage(5000);
    }, JobMessageNotFoundException::class);
  }
}

$test = new JobTest;
$test->run();
?>