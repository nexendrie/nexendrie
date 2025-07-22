<?php
declare(strict_types=1);

namespace Nexendrie\Presenters;

use Nette\Application\Responses\RedirectResponse;
use Testbench\TPresenter as BaseTrait;
use Nexendrie\Model\TUserControl;
use Tester\Assert;
use Nexendrie\Rss\Bridges\NetteApplication\RssResponse;
use Nette\Application\Responses\ForwardResponse;
use Nette\Application\Response;

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
  
  /**
   * @throws \Exception
   */
  protected function checkRss(string $destination, array $params = [], array $post = []): RssResponse {
    /** @var RssResponse $response */
    $response = $this->check($destination, $params, $post);
    if(!$this->testbench_exception) {
      Assert::same(200, $this->getReturnCode());
      Assert::type(RssResponse::class, $response);
      
      $dom = \Tester\DomQuery::fromXml($response->source);
      Assert::true($dom->has('rss'), "missing 'rss' element");
      Assert::true($dom->has('channel'), "missing 'channel' element");
      Assert::true($dom->has('title'), "missing 'title' element");
      Assert::true($dom->has('link'), "missing 'link' element");
      Assert::true($dom->has('item'), "missing 'item' element");
    }
    return $response;
  }
  
  /**
   * @throws \Exception
   */
  protected function checkForward(string $destination, string $to = "", array $params = [], array $post = []): ForwardResponse {
    /** @var ForwardResponse $response */
    $response = $this->check($destination, $params, $post);
    if(!$this->testbench_exception) {
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
  
  /**
   * @param bool|string $redirect
   * @return RedirectResponse
   * @throws \Exception
   */
  protected function checkSignal(string $destination, string $signal, array $params = [], array $post = [], bool $redirect = false): Response {
    return $this->checkRedirect($destination, $redirect, [
        "do" => $signal,
      ] + $params, $post);
  }
}
?>