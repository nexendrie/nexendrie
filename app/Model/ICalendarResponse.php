<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette;
use Nette\Application\IResponse;

final class ICalendarResponse implements IResponse {
  use \Nette\SmartObject;

  private string $source;

  public function __construct(string $source) {
    $this->source = $source;
  }

  protected function getSource(): string {
    return $this->source;
  }

  public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void {
    $httpResponse->setContentType("text/calendar");
    echo $this->source;
  }
}
?>