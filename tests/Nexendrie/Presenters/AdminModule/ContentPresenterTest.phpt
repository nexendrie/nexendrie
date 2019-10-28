<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

require __DIR__ . "/../../../bootstrap.php";

final class ContentPresenterTest extends \Tester\TestCase {
  use TAdminPresenter;
  
  public function testDefault() {
    $this->defaultChecks(":Admin:Content:default");
  }
  
  public function testShops() {
    $this->defaultChecks(":Admin:Content:shops");
  }
  
  public function testItems() {
    $this->defaultChecks(":Admin:Content:items");
  }
  
  public function testJobs() {
    $this->defaultChecks(":Admin:Content:jobs");
  }
  
  public function testTowns() {
    $this->defaultChecks(":Admin:Content:towns");
  }
  
  public function testMounts() {
    $this->defaultChecks(":Admin:Content:mounts");
  }
  
  public function testSkills() {
    $this->defaultChecks(":Admin:Content:skills");
  }
  
  public function testMeals() {
    $this->defaultChecks(":Admin:Content:meals");
  }
  
  public function testAdventures() {
    $this->defaultChecks(":Admin:Content:adventures");
  }
  
  public function testItemSets() {
    $this->defaultChecks(":Admin:Content:itemSets");
  }
  
  public function testGift() {
    $this->defaultChecks(":Admin:Content:gift", ["id" => 1]);
  }

  public function testReported() {
    $this->defaultChecks(":Admin:Content:reported");
  }

  public function testSignalDelete() {
    $this->checkSignal(":Admin:Content:delete", "delete", ["report" => 50], [], "/user/login");
    $this->login("kazimira");
    $this->checkSignal(":Admin:Content:delete", "delete", ["report" => 50], [], "/");
    $this->login();
    $this->checkSignal(":Admin:Content:delete", "delete", ["report" => 50], [], "/admin/");
  }

  public function testSignalIgnore() {
    $this->checkSignal(":Admin:Content:delete", "ignore", ["report" => 50], [], "/user/login");
    $this->login("kazimira");
    $this->checkSignal(":Admin:Content:delete", "ignore", ["report" => 50], [], "/");
    $this->login();
    $this->checkSignal(":Admin:Content:delete", "ignore", ["report" => 50], [], "/admin/");
  }
}

$test = new ContentPresenterTest();
$test->run();
?>