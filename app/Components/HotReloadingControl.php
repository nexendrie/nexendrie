<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nette\Application\UI\Control;

/**
 * HotReloadingControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class HotReloadingControl extends Control
{
    public ?string $url = null;

    public function render(): void
    {
        $this->template->setFile(__DIR__ . "/hotReloading.latte");
        $this->template->url = $this->url;
        $this->template->render();
    }
}
