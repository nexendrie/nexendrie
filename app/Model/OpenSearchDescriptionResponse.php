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
final class OpenSearchDescriptionResponse implements \Nette\Application\IResponse {
  use \Nette\SmartObject;

  /** @var string */
  private $source;

  public function __construct(string $source) {
    $this->source = $source;
  }

  public function getSource(): string {
    return $this->source;
  }

  public function send(IRequest $httpRequest, IResponse $httpResponse): void {
    $httpResponse->setContentType("application/opensearchdescription+xml");
    echo $this->source;
  }
}
?>