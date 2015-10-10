<?php
namespace Nexendrie\Model;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

/**
 * Router Factory
 *
 * @author Jakub Konečný
 */
class RouterFactory extends \Nette\Object {
  /**
   * @return \Nette\Application\Routers\RouteList
   */
  static function create() {
    $router = new RouteList;
    $router[] = new Route("profile/<username>", "Front:Profile:default");
    $router[] = new Route("message/<id [0-9]+>", "Front:Messages:view");
    $router[] = new Route("poll/<id [0-9]+>", "Front:Poll:view");
    $router[] = new Route("news/page/<page [0-9]+>", "Front:News:page");
    $router[] = new Route("rss[/<action>][/<news [0-9]+>]", "Front:Rss:news");
    $router[] = new Route("admin/groups", "Admin:Group:default");
    $router[] = new Route("admin/users", "Admin:User:default");
    $router[] = new Route("admin/content/<presenter shop>/<action>/<id>" , array(
      "module" => "Admin"));
    $router[] = new Route("admin/<presenter>[/<action>][/<id>]", array(
      "module" => "Admin", "presenter" => "Homepage", "action" => "default")
    );
    $router[] = new Route("<presenter>[/<action>][/<id>]", array(
      "module" => "Front", "presenter" => "Homepage", "action" => "default")
    );
    return $router;
  }
}
?>