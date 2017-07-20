<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Cron
 *
 * @author Jakub Konečný
 */
class CronPresenter extends BasePresenter {
  /** @var \stekycz\Cronner\Cronner @autowire */
  protected $cronner;
  
  public function actionDefault(): void {
    $this->cronner->run();
  }
}
?>