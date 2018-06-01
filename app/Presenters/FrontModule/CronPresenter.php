<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Cron
 *
 * @author Jakub Konečný
 */
final class CronPresenter extends BasePresenter {
  /** @var \stekycz\Cronner\Cronner */
  protected $cronner;
  
  public function __construct(\stekycz\Cronner\Cronner $cronner) {
    parent::__construct();
    $this->cronner = $cronner;
  }
  
  public function actionDefault(): void {
    $this->cronner->run();
  }
}
?>