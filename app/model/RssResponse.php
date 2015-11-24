<?php
namespace Nexendrie\Responses;

/**
 * RSS channel response
 *
 * @author Jakub Konečný
 * 
 * @property-read \SimpleXMLElement $source
 */
class RssResponse extends \Nette\Object implements \Nette\Application\IResponse {
  /** @var \SimpleXMLElement */
  private $source;
  
  /**
   * @param \SimpleXMLElement $source
   */
  function __construct(\SimpleXMLElement $source) {
   $this->source = $source;
  }
  
  /**
   * @return \SimpleXMLElement
   */
  function getSource() {
    return $this->source;
  }
  
  /**
   * Sends response to output
   * 
   * @param \Nette\Http\IRequest $httpRequest
   * @param \Nette\Http\IResponse $httpResponse
   * @return void
   */
  function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse) {
    $httpResponse->contentType = "application/xhtml+xml";
    echo $this->source->asXML();
  }
}
?>