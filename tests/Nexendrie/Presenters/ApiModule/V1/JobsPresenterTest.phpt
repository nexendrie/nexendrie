<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class JobsPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  protected function checkJobs(\Nette\Application\Responses\JsonResponse $response, int $count): void {
    $json = $response->getPayload();
    Assert::type("array", $json["jobs"]);
    Assert::count($count, $json["jobs"]);
    foreach($json["jobs"] as $job) {
      Assert::type(\stdClass::class, $job);
      Assert::type(\stdClass::class, $job->neededSkill);
    }
  }
  
  public function testReadAll() {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $this->checkJobs($response, 13);
    $response = $this->checkJson($action, ["associations" => ["skills" => 1]]);
    $this->checkJobs($response, 1);
    $expected = ["message" => "Skill with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["skills" => 50]]);
  }
  
  public function testRead() {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["job"]);
    Assert::type(\stdClass::class, $json["job"]->neededSkill);
    $expected = ["message" => "Job with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new JobsPresenterTest();
$test->run();
?>