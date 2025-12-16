<?php
declare(strict_types=1);

namespace Nexendrie\Presenters;

use Nette\Application\Responses\RedirectResponse;
use Nexendrie\Components\FaviconControl;
use Nexendrie\Components\FaviconControlFactory;
use Nexendrie\Menu\IMenuControlFactory;
use Nexendrie\Menu\MenuControl;
use Nexendrie\Components\UserProfileLinkControlFactory;
use Nexendrie\Components\UserProfileLinkControl;

/**
 * Ultimate ancestor of all presenters
 *
 * @author Jakub Konečný
 * @property-read \Nette\Application\UI\Template $template
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    use \Kdyby\Autowired\AutowireComponentFactories;

    protected \Nexendrie\Model\SettingsRepository $sr;
    protected \Nexendrie\Model\ThemesManager $themesManager;
    protected UserProfileLinkControlFactory $userProfileLinkFactory;
    protected bool $cachingEnabled;
    protected bool $publicCache = true;
    protected bool $earlyHints;

    public function injectSettingsRepository(\Nexendrie\Model\SettingsRepository $sr): void
    {
        $this->sr = $sr;
        if (!isset($this->cachingEnabled)) {
            $this->cachingEnabled = (bool) $this->sr->settings["features"]["httpCaching"];
        }
        if (!isset($this->earlyHints)) {
            $this->earlyHints = (bool) $this->sr->settings["features"]["earlyHints"];
        }
    }

    public function injectUserProfileLinkFactory(UserProfileLinkControlFactory $userProfileLinkFactory): void
    {
        $this->userProfileLinkFactory = $userProfileLinkFactory;
    }

    public function injectThemesManager(\Nexendrie\Model\ThemesManager $themesManager): void
    {
        $this->themesManager = $themesManager;
    }

    public function storeRequest(string $expiration = "+ 10 minutes"): string
    {
        $session = $this->getSession("Nette.Application/requests");
        do {
            $key = \Nette\Utils\Random::generate(5);
        } while (isset($session[$key]));

        $session[$key] = [$this->request];
        $session->setExpiration($expiration, $key);
        return $key;
    }

    protected function getFlashKey(): ?string
    {
        $flashKey = $this->getParameter(self::FLASH_KEY);
        return (is_string($flashKey) && $flashKey !== "") ? $flashKey : null;
    }

    public function restoreRequest(string $key): void
    {
        $session = $this->getSession("Nette.Application/requests");
        if (!isset($session[$key])) {
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

    protected function getCurrentTheme(): string
    {
        if (!$this->user->isLoggedIn()) {
            return $this->sr->settings["newUser"]["style"];
        }
        return $this->user->identity->style;
    }

    /**
     * @return string[]
     */
    protected function getEarlyScripts(): array
    {
        return [];
    }

    /**
     * Set website's style
     */
    protected function startup(): void
    {
        parent::startup();
        $style = $this->themesManager->getThemeFileUrl($this->getCurrentTheme());
        $this->template->style = $style;
        if ($this->earlyHints) {
            $linkHeader = "<$style>; rel=preload; as=style";
            $earlyScripts = $this->getEarlyScripts();
            foreach ($earlyScripts as $earlyScript) {
                $linkHeader .= ", <$earlyScript>; rel=preload; as=script";
            }
            $this->getHttpResponse()->setHeader("Link", $linkHeader);
            if (extension_loaded("frankenphp") && function_exists("headers_send")) {
                headers_send(103);
            }
        }
    }

    protected function beforeRender(): void
    {
        $this->getHttpResponse()->setHeader("Content-Language", "cs,sk");
        $versionSuffix = $this->sr->settings["site"]["versionSuffix"];
        $this->template->siteName = trim("Nexendrie " . $versionSuffix);
    }

    public function sendResponse(\Nette\Application\Response $response): never
    {
        if (!$response instanceof RedirectResponse && $this->getHttpResponse()->getCode() === \Nette\Http\IResponse::S200_OK) {
            $this->lastModified();
        }
        parent::sendResponse($response);
    }

    /**
     * @param string|int|\DateTimeInterface $lastModified
     */
    public function lastModified($lastModified = 0, string $etag = null, string $expire = null): void
    {
        $this->getHttpResponse()->deleteHeader("Pragma");
        if (!$this->cachingEnabled) {
            return;
        }
        $this->getHttpResponse()->setHeader("Vary", $this->getHttpResponse()->getHeader("Vary") . ", Cookie");
        $this->getHttpResponse()->setHeader("Cache-Control", ($this->publicCache) ? "public" : "private");
        if ($lastModified === 0) {
            $lastModified = $this->getModifiedTime();
        }
        parent::lastModified($lastModified, $etag, $expire);
    }

    protected function getModifiedTime(): int
    {
        return max($this->getTemplateModifiedTime(), $this->getDataModifiedTime());
    }

    protected function getTemplateModifiedTime(): int
    {
        if (!$this->cachingEnabled) {
            return 0;
        }
        $time = 0;
        $filename = $this->template->getFile();
        if ($filename !== null) {
            $time = filemtime($filename);
        }
        return (int) $time;
    }

    protected function getDataModifiedTime(): int
    {
        return 0;
    }

    /**
     * The user must have specified rights to see a page
     */
    protected function requiresPermissions(string $resource, string $action): void
    {
        if (!$this->user->isAllowed($resource, $action)) {
            $this->flashMessage("K zobrazení této stránky nemáš práva.");
            $this->redirect("Homepage:");
        }
    }

    protected function createComponentMenu(IMenuControlFactory $factory): MenuControl
    {
        return $factory->create();
    }

    protected function createComponentUserProfileLink(): UserProfileLinkControl
    {
        return $this->userProfileLinkFactory->create();
    }

    protected function createComponentFavicon(FaviconControlFactory $factory): FaviconControl
    {
        return $factory->create();
    }
}
