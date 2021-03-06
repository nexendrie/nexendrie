<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Cron
 *
 * @author Jakub Konečný
 */
final class CronPresenter extends \Nette\Application\UI\Presenter {
  protected \stekycz\Cronner\Cronner $cronner;
  
  public function __construct(\stekycz\Cronner\Cronner $cronner) {
    parent::__construct();
    $this->cronner = $cronner;
  }
  
  public function actionDefault(): void {
    $this->cronner->run();
  }
}
?>