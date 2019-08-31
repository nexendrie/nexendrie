<?php
declare(strict_types=1);

namespace Nexendrie\Api;

use Nette\Http\IRequest;

final class RestRoute extends \AdamStipak\RestRoute {
  protected function detectAction(IRequest $request) {
    try {
      return parent::detectAction($request);
    } catch(\Nette\InvalidStateException $e) {
      if($this->detectMethod($request) === "HEAD") {
        return "read";
      }
      throw $e;
    }
  }
}
?>