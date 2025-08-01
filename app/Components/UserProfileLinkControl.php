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
  private function baseRender(string $id, string $name, string|bool $title): void {
    $this->template->setFile(__DIR__ . "/userProfileLink.latte");
    $this->template->id = $id;
    $this->template->name = $name;
    $this->template->title = $title;
    $this->template->render();
  }
  
  public function render(User $user, bool $withTitle = false): void {
    $title = (($withTitle) ? $user->title : false);
    $this->baseRender($user->publicname, $user->publicname, $title);
  }
  
  public function renderName(User $user, string $name): void {
    $this->baseRender($user->publicname, $name, false);
  }
  
  public function renderGuild(User $user): void {
    $title = (($user->guildRank !== null) ? $user->guildRank->name : false);
    $this->baseRender($user->publicname, $user->publicname, $title);
  }
  
  public function renderOrder(User $user): void {
    $title = (($user->orderRank !== null) ? $user->orderRank->name : false);
    $this->baseRender($user->publicname, $user->publicname, $title);
  }
  
  public function renderPage(User $user, string $page, string $text): void {
    $this->template->setFile(__DIR__ . "/userProfileLinkPage.latte");
    $this->template->id = $user->publicname;
    $this->template->page = $page;
    $this->template->text = $text;
    $this->template->render();
  }
}
?>