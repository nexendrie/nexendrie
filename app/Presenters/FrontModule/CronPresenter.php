<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Cron
 *
 * @author Jakub Konečný
 */
final class CronPresenter extends \Nette\Application\UI\Presenter {
  public function __construct(private readonly \stekycz\Cronner\Cronner $cronner) {
    parent::__construct();
  }
  
  public function actionDefault(): void {
    $this->cronner->run();
  }
}
?>