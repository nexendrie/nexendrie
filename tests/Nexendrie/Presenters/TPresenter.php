<?php
declare(strict_types=1);

namespace Nexendrie\Presenters;

use Testbench\TPresenter as BaseTrait,
    Nexendrie\Model\TUserControl,
    Tester\Assert,
    Nexendrie\Rss\RssResponse,
    Nette\Application\Responses\ForwardResponse;

/**
 * TPresenter
 *
 * @author Jakub Konečný
 */
trait TPresenter {
  use BaseTrait, TUserControl {
    TUserControl::login insteadof BaseTrait;
    TUserControl::logout insteadof BaseTrait;
  }
  
  protected function checkRss(string $destination, array $params = [], array $post = []): RssResponse {
    /** @var RssResponse $response */
    $response = $this->check($destination, $params, $post);
    if(!$this->__testbench_exception) {
      Assert::same(200, $this->getReturnCode());
      Assert::type(RssResponse::class, $response);
      
      $dom = \Tester\DomQuery::fromXml($response->getSource()->asXML());
      Assert::true($dom->has('rss'), "missing 'rss' element");
      Assert::true($dom->has('channel'), "missing 'channel' element");
      Assert::true($dom->has('title'), "missing 'title' element");
      Assert::true($dom->has('link'), "missing 'link' element");
      Assert::true($dom->has('item'), "missing 'item' element");
    }
    return $response;
  }
  
  protected function checkForward(string $destination, string $to = "", array $params = [], array $post = []) {
    /** @var ForwardResponse $response */
    $response = $this->check($destination, $params, $post);
    if(!$this->__testbench_exception) {
      Assert::same(200, $this->getReturnCode());
      Assert::type(ForwardResponse::class, $response);
      if($to) {
        $target = $response->getRequest()->presenterName . ":" . $response->getRequest()->parameters["action"];
        if($to !== $target) {
          Assert::fail("does not forward to $to, but {$target}");
        }
      }
    }
    return $response;
  }
}
?>