<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * BasePresenter
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nexendrie\Presenters\ApiModule\BasePresenter {
  final protected function getApiVersion(): string {
    return "v1";
  }
}
?>