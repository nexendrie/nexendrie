<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * OpenSearch description response
 *
 * @author Jakub Konečný
 *
 * @property-read string $source
 */
final class OpenSearchDescriptionResponse implements \Nette\Application\Response {
  use \Nette\SmartObject;

  public function __construct(public readonly string $source) {
  }

  public function send(IRequest $httpRequest, IResponse $httpResponse): void {
    $httpResponse->setContentType("application/opensearchdescription+xml");
    echo $this->source;
  }
}
?>