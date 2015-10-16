<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Cron
 *
 * @author Jakub Konečný
 */
class CronPresenter extends BasePresenter {
  /** @var \stekycz\Cronner\Cronner @autowire */
  protected $cronner;
  
  /**
   * @return void
   */
  function actionDefault() {
    $this->cronner->run();
  }
}
?>