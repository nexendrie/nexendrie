<?php
declare(strict_types=1);

namespace Nexendrie\Api;

use Nette\Http\IRequest;

final class RestRoute extends \AdamStipak\RestRoute {
  protected function detectAction(IRequest $request): ?string {
    if($this->detectMethod($request) === "HEAD") {
      return "read";
    }
    return parent::detectAction($request);
  }
}
?>