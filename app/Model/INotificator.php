<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Structs\Notification;

interface INotificator {
  /**
   * @return Notification[]
   */
  public function getNotifications(): array;
}
?>