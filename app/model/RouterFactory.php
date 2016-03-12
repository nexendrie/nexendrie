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
    $router[] = new Route("/", "Front:Homepage:page");
    $router[] = new Route("profile/<username>", "Front:Profile:default");
    $router[] = new Route("<presenter message|poll|article|event>/<id [0-9]+>", array(
      "module" => "Front", "action" => "view",
      "presenter" => array(
        Route::FILTER_TABLE => array(
          "message" => "Messages"
        )
      ) 
    ));
    $router[] = new Route("page/<page [0-9]+>", "Front:Homepage:page");
    $router[] = new Route("rss[/<action>][/<news [0-9]+>]", "Front:Rss:news");
    $router[] = new Route("<presenter help|history>[/<page=index>]", array(
      "module" => "Front", "action" => "default"
    ));
    $router[] = new Route("admin/<presenter groups|users|events>", array(
      "module" => "Admin", "action" => "default",
      "presenter" => array(
        Route::FILTER_TABLE => array(
          "groups" => "Group",
          "users" => "User",
          "events" => "Event"
        )
      )
    ));
    $router[] = new Route("admin/content/<presenter shop|item|job|jobMessages|town|mount|skill|adventure|adventureEnemies>/<action>[/<id>]" , array(
      "module" => "Admin"
    ));
    $router[] = new Route("admin/<presenter>[/<action>][/<id>]", array(
      "module" => "Admin", "presenter" => "Homepage", "action" => "default"
    ));
    $router[] = new Route("<presenter>[/<action>][/<id>]", array(
      "module" => "Front", "presenter" => "Homepage", "action" => "default"
    ));
    return $router;
  }
}
?>