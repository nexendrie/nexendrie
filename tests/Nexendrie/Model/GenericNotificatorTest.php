<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Structs\Notification;
use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Message;

require __DIR__ . "/../../bootstrap.php";

final class GenericNotificatorTest extends \Tester\TestCase {
  use TUserControl;

  private GenericNotificator $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(GenericNotificator::class); // @phpstan-ignore assign.propertyType
  }

  public function testNotifications(): void {
    Assert::exception(function() {
      $this->model->getNotifications();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->getNotifications();
    Assert::type("array", $result);
    Assert::count(0, $result);
    $notification = new Notification();
    $notification->title = "Test notification";
    $notification->body = "Test text";
    $notification->tag = "test";
    $this->model->createNotification($notification, $this->getUser()->id);
    $result = $this->model->getNotifications();
    Assert::type("array", $result);
    Assert::count(0, $result);
    $this->modifyUser(["notifications" => true,], function() use ($notification) {
      $this->model->createNotification($notification, $this->getUser()->id);
      $result = $this->model->getNotifications();
      Assert::type("array", $result);
      Assert::count(1, $result);
      Assert::same($notification->title, $result[0]->title);
      Assert::same($notification->body, $result[0]->body);
      Assert::same($notification->tag, $result[0]->tag);
      Assert::same($notification->targetUrl, $result[0]->targetUrl);
    });
  }
}

$test = new GenericNotificatorTest();
$test->run();
?>