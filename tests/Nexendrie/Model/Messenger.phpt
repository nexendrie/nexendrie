<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Message;

require __DIR__ . "/../../bootstrap.php";

class MessengerTest extends \Tester\TestCase {
  use \TUserControl;
  
  /** @var Messenger */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Messenger::class);
  }
  
  function testInbox() {
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
  
  function testOutbox() {
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
  
  function testShow() {
    Assert::exception(function() {
      $this->model->show(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->show(50);
    }, MessageNotFoundException::class);
    $message = $this->model->show(1);
    Assert::type(Message::class, $message);
    $this->login("system");
    Assert::exception(function() {
      $this->model->show(1);
    }, AccessDeniedException::class);
  }
  
  function testUsersList() {
    $result = $this->model->usersList();
    Assert::type("array", $result);
    Assert::count(7, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
}

$test = new MessengerTest;
$test->run();
?>