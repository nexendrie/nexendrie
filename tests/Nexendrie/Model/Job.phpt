<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nextras\Orm\Relationships\OneHasMany,
    Nexendrie\Orm\Job as JobEntity,
    Nexendrie\Orm\JobMessage;

require __DIR__ . "/../../bootstrap.php";

class JobTest extends \Tester\TestCase {
  use \TUserControl;
  
  /** @var Job */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Job::class);
  }
  
  function testListOfJobs() {
    $result = $this->model->listOfJobs();
    Assert::type(ICollection::class, $result);
    Assert::type(JobEntity::class, $result->fetch());
  }
  
  function testCalculateAward() {
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
  
  function testFindAvailableJobs() {
    Assert::exception(function() {
      $this->model->findAvailableJobs();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->findAvailableJobs();
    Assert::type("array", $result);
    Assert::type(\stdClass::class, $result[0]);
  }
  
  function testGetJob() {
    $job = $this->model->getJob(1);
    Assert::type(JobEntity::class, $job);
    Assert::exception(function() {
      $this->model->getJob(50);
    }, JobNotFoundException::class);
  }
  
  function testEditJob() {
    Assert::exception(function() {
      $this->model->editJob(50, []);
    }, JobNotFoundException::class);
  }
  
  function testIsWorking() {
    Assert::exception(function() {
      $this->model->isWorking();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("bool", $this->model->isWorking());
  }
  
  
  function testListOfMessages() {
    $result = $this->model->listOfMessages(1);
    Assert::type(OneHasMany::class, $result);
    Assert::type(JobMessage::class, $result->get()->fetch());
    Assert::exception(function() {
      $this->model->listOfMessages(50);
    }, JobNotFoundException::class);
  }
  
  function testGetMessage() {
    $message = $this->model->getMessage(1);
    Assert::type(JobMessage::class, $message);
    Assert::exception(function() {
      $this->model->getMessage(50);
    }, JobMessageNotFoundException::class);
  }
}

$test = new JobTest;
$test->run();
?>