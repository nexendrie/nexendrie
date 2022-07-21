<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\INotificator;

class SsePresenter extends BasePresenter {
  /** @var INotificator[] */
  private array $notificators;

  /**
   * @param INotificator[] $notificators
   */
  public function __construct(array $notificators) {
    parent::__construct();
    $this->notificators = $notificators;
  }

  public function actionDefault(): void {
    header("Cache-Control: no-store");
    header("Content-Type: text/event-stream");
    while(true) {
      if(!$this->user->isLoggedIn() || !$this->user->identity->notifications) {
        break;
      }

      echo "event: ping\ndata: nothing to see\n\n";

      foreach($this->notificators as $notificator) {
        $notifications = $notificator->getNotifications();
        foreach($notifications as $notification) {
          echo "event: notification\ndata: " . json_encode($notification) . "\n\n";
        }
      }

      ob_end_flush();
      flush();

      if(connection_aborted() === 1) {
        break;
      }
      sleep($this->sr->settings["site"]["serverSideEventsCooldown"]);
    }
  }
}
?>