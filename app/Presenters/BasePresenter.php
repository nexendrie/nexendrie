<?php
declare(strict_types=1);

namespace Nexendrie\Presenters;

use Nexendrie\Menu\IMenuControlFactory;
use Nexendrie\Menu\MenuControl;
use Nexendrie\Components\IUserProfileLinkControlFactory;
use Nexendrie\Components\UserProfileLinkControl;

/**
 * Ultimate ancestor of all presenters
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  use \Kdyby\Autowired\AutowireComponentFactories;
  
  /** @var \Nexendrie\Model\SettingsRepository */
  protected $sr;
  /** @var IUserProfileLinkControlFactory */
  protected $userProfileLinkFactory;
  
  public function injectSettingsRepository(\Nexendrie\Model\SettingsRepository $sr): void {
    $this->sr = $sr;
  }
  
  public function injectUserProfileLinkFactory(IUserProfileLinkControlFactory $userProfileLinkFactory): void {
    $this->userProfileLinkFactory = $userProfileLinkFactory;
  }

  public function storeRequest($expiration = "+ 10 minutes"): string {
    $session = $this->getSession("Nette.Application/requests");
    do {
      $key = \Nette\Utils\Random::generate(5);
    } while (isset($session[$key]));

    $session[$key] = [$this->request];
    $session->setExpiration($expiration, $key);
    return $key;
  }

  protected function getFlashKey(): ?string {
    $flashKey = $this->getParameter(self::FLASH_KEY);
    return (is_string($flashKey) && $flashKey !== "") ? $flashKey : null;
  }

  public function restoreRequest($key): void {
    $session = $this->getSession("Nette.Application/requests");
    if(!isset($session[$key])) {
      return;
    }
    /** @var \Nette\Application\Request $request */
    $request = clone $session[$key][0];
    unset($session[$key]);
    $request->setFlag(\Nette\Application\Request::RESTORED, true);
    $params = $request->getParameters();
    $params[self::FLASH_KEY] = $this->getFlashKey();
    $request->setParameters($params);
    $this->sendResponse(new \Nette\Application\Responses\ForwardResponse($request));
  }
  
  /**
   * Set website's style
   */
  protected function startup(): void {
    parent::startup();
    $this->template->style = $this->sr->settings["newUser"]["style"];
    if($this->user->isLoggedIn()) {
      $this->template->style = $this->user->identity->style;
    }
  }
  
  protected function beforeRender(): void {
    $this->getHttpResponse()->setHeader("Content-Language", "cs,sk");
    $this->template->versionSuffix = $this->sr->settings["site"]["versionSuffix"];
  }
  
  /**
   * The user must have specified rights to see a page
   */
  protected function requiresPermissions(string $resource, string $action): void {
    if(!$this->user->isAllowed($resource, $action)) {
      $this->flashMessage("K zobrazení této stránky nemáš práva.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentMenu(IMenuControlFactory $factory): MenuControl {
    return $factory->create();
  }
  
  protected function createComponentUserProfileLink(): UserProfileLinkControl {
    return $this->userProfileLinkFactory->create();
  }
}
?>