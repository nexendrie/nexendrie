<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nette\Application\UI\Control;
use Nexendrie\Orm\User;

/**
 * UserProfileLinkControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class UserProfileLinkControl extends Control {
  /**
   * @param string|bool $title
   */
  protected function baseRender(object $user, $title): void {
    $this->template->setFile(__DIR__ . "/userProfileLink.latte");
    $this->template->user = $user;
    $this->template->title = $title;
    $this->template->render();
  }
  
  public function render(User $user, bool $withTitle = false): void {
    $title = (($withTitle) ? $user->title : false);
    $this->baseRender($user, $title);
  }
  
  public function renderName(User $user, string $name): void {
    $user = (object) [
      "username" => $user->username, "publicname" => $name,
    ];
    $this->baseRender($user, false);
  }
  
  public function renderGuild(User $user): void {
    $title = (($user->guildRank) ? $user->guildRank->name : false);
    $this->baseRender($user, $title);
  }
  
  public function renderOrder(User $user): void {
    $title = (($user->orderRank) ? $user->orderRank->name : false);
    $this->baseRender($user, $title);
  }
  
  public function renderPage(User $user, string $page, string $text): void {
    $this->template->setFile(__DIR__ . "/userProfileLinkPage.latte");
    $this->template->user = $user;
    $this->template->page = $page;
    $this->template->text = $text;
    $this->template->render();
  }
}
?>