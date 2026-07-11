<?php
declare(strict_types=1);

namespace Nexendrie\Components;

/**
 * SocialIconsControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\DefaultTemplate $template
 */
final class SocialIconsControl extends \Nette\Application\UI\Control
{
    /**
     * @param SocialIcon[] $icons
     */
    public function __construct(private readonly array $icons)
    {
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . "/socialIcons.latte");
        $this->template->icons = $this->icons;
        $this->template->render();
    }
}
