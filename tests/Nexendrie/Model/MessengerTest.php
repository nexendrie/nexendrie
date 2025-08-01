<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Message;

require __DIR__ . "/../../bootstrap.php";

final class MessengerTest extends \Tester\TestCase {
  use TUserControl;

  protected Messenger $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Messenger::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testInbox(): void {
    Assert::exception(function() {
      $this->model->inbox();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->inbox();
    Assert::type(ICollection::class, $result);
    /** @var Message $message */
    $message = $result->fetch();
    Assert::type(Message::class, $message);
    Assert::same(1, $message->to->id);
  }
  
  public function testOutbox(): void {
    Assert::exception(function() {
      $this->model->outbox();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->outbox();
    Assert::type(ICollection::class, $result);
    /** @var Message $message */
    $message = $result->fetch();
    Assert::type(Message::class, $message);
    Assert::same(1, $message->from->id);
  }
  
  public function testShow(): void {
    Assert::exception(function() {
      $this->model->show(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->show(50);
    }, MessageNotFoundException::class);
    $message = $this->model->show(1);
    Assert::type(Message::class, $message);
    $this->login("Vladěna");
    Assert::exception(function() {
      $this->model->show(1);
    }, AccessDeniedException::class);
  }
  
  public function testUsersList(): void {
    $this->login();
    $result = $this->model->usersList();
    Assert::type("array", $result);
    Assert::count(6, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
}

$test = new MessengerTest();
$test->run();
?>