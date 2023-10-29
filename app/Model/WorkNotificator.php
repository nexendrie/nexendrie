<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Application\LinkGenerator;
use Nexendrie\Structs\Notification;
use Nexendrie\Utils\Numbers;

final class WorkNotificator implements INotificator {
  public const TAG_WORK_FINISHED = "workFinished";
  public const TAG_WORK_NEXT_SHIFT = "workNextShift";

  private Job $job;
  private LinkGenerator $linkGenerator;
  private SettingsRepository $sr;

  public function __construct(Job $job, LinkGenerator $linkGenerator, SettingsRepository $sr) {
    $this->job = $job;
    $this->linkGenerator = $linkGenerator;
    $this->sr = $sr;
  }

  public function getNotifications(): array {
    try {
      $currentJob = $this->job->getCurrentJob();
    } catch (NotWorkingException $e) {
      return [];
    }
    $notifications = [];
    $targetUrl = $this->linkGenerator->link("Front:Work:default");
    if (Numbers::isInRange(time(), $currentJob->finishTime, $currentJob->finishTime + $this->sr->settings["site"]["serverSideEventsCooldown"])) {
      $notification = new Notification();
      $notification->title = "Práce dokončena na " . $this->getSiteName();
      $notification->body = "Práce byla dokončena. Můžeš si vyzvednout odměnu.";
      $notification->tag = self::TAG_WORK_FINISHED;
      $notification->targetUrl = $targetUrl;
      $notifications[] = $notification;
    } elseif (Numbers::isInRange(time(), $currentJob->nextShiftTime, $currentJob->nextShiftTime + $this->sr->settings["site"]["serverSideEventsCooldown"])) {
      $notification = new Notification();
      $notification->title = "Další směna na " . $this->getSiteName();
      $notification->body = "Je čas na další směnu v práci.";
      $notification->tag = self::TAG_WORK_NEXT_SHIFT;
      $notification->targetUrl = $targetUrl;
      $notifications[] = $notification;
    }
    return $notifications;
  }

  public function getSiteName(): string {
    return trim("Nexendrie " . $this->sr->settings["site"]["versionSuffix"]);
  }
}
?>