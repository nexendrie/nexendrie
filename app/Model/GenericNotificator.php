<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Security\User;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Structs\Notification;

final class GenericNotificator implements INotificator {
  public function __construct(private readonly ORM $orm, private readonly User $user, private readonly SettingsRepository $sr) {
  }

  public function createNotification(Notification $data, int $userId): void {
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($userId);
    if(!$user->notifications) {
      return;
    }
    $notification = new \Nexendrie\Orm\Notification();
    $notification->title = $data->title;
    $notification->body = $data->body;
    $notification->icon = $data->icon;
    $notification->tag = $data->tag;
    $notification->targetUrl = $data->targetUrl;
    $notification->user = $user;
    $this->orm->notifications->persistAndFlush($notification);
  }

  public function getNotifications(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    $notifications = [];
    $entities = $this->orm->notifications->findByUser($this->user->id);
    foreach($entities as $entity) {
      $notification = new Notification();
      $notification->title = $entity->title;
      $notification->body = $entity->body;
      $notification->icon = $entity->icon;
      $notification->tag = $entity->tag;
      $notification->targetUrl = $entity->targetUrl;
      $notifications[] = $notification;
      $this->orm->removeAndFlush($entity);
    }
    return $notifications;
  }

  public function getSiteName(): string {
    return trim("Nexendrie " . $this->sr->settings["site"]["versionSuffix"]);
  }
}
?>