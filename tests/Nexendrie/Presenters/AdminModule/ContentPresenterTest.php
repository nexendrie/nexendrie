<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class ContentPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault(): void {
    $this->defaultChecks(":Admin:Content:default");
  }
  
  public function testShops(): void {
    $this->defaultChecks(":Admin:Content:shops");
  }
  
  public function testItems(): void {
    $this->defaultChecks(":Admin:Content:items");
  }
  
  public function testJobs(): void {
    $this->defaultChecks(":Admin:Content:jobs");
  }
  
  public function testTowns(): void {
    $this->defaultChecks(":Admin:Content:towns");
  }
  
  public function testMounts(): void {
    $this->defaultChecks(":Admin:Content:mounts");
  }
  
  public function testSkills(): void {
    $this->defaultChecks(":Admin:Content:skills");
  }
  
  public function testMeals(): void {
    $this->defaultChecks(":Admin:Content:meals");
  }
  
  public function testAdventures(): void {
    $this->defaultChecks(":Admin:Content:adventures");
  }
  
  public function testItemSets(): void {
    $this->defaultChecks(":Admin:Content:itemSets");
  }
  
  public function testGift(): void {
    $this->defaultChecks(":Admin:Content:gift", ["id" => 1]);
  }

  public function testReported(): void {
    $this->defaultChecks(":Admin:Content:reported");
  }

  public function testSignalDelete(): void {
    $this->checkSignal(":Admin:Content:delete", "delete", ["report" => 50], [], "/user/login");
    $this->login("kazimira");
    $this->checkSignal(":Admin:Content:delete", "delete", ["report" => 50], [], "/");
    $this->login();
    $this->checkSignal(":Admin:Content:delete", "delete", ["report" => 50], [], "/admin/content/delete");
  }

  public function testSignalIgnore(): void {
    $this->checkSignal(":Admin:Content:delete", "ignore", ["report" => 50], [], "/user/login");
    $this->login("kazimira");
    $this->checkSignal(":Admin:Content:delete", "ignore", ["report" => 50], [], "/");
    $this->login();
    $this->checkSignal(":Admin:Content:delete", "ignore", ["report" => 50], [], "/admin/content/delete");
  }
}

$test = new ContentPresenterTest();
$test->run();
?>