<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

/**
 * TAdminPresenter
 *
 * @author Jakub Konečný
 */
trait TAdminPresenter {
  use \Nexendrie\Presenters\TPresenter;
  
  protected function defaultChecks(string $action, array $params = []): void {
    $this->checkRedirect($action, "/user/login", $params);
    $this->login("kazimira");
    $this->checkRedirect($action, "/", $params);
    $this->login();
    $this->checkAction($action, $params);
  }
}
?>