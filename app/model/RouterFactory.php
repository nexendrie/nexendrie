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
    $frontRouter = new RouteList("Front");
    $frontRouter[] = new Route("/", "Homepage:page");
    $frontRouter[] = new Route("profile/<username>", "Profile:default");
    $frontRouter[] = new Route("<presenter message|poll|article|event>/<id [0-9]+>", array(
      "action" => "view",
      "presenter" => array(
        Route::FILTER_TABLE => array(
          "message" => "Messages"
        )
      ) 
    ));
    $frontRouter[] = new Route("page/<page [0-9]+>", "Homepage:page");
    $frontRouter[] = new Route("rss[/<action>][/<news [0-9]+>]", "Rss:news");
    $frontRouter[] = new Route("<presenter help|history>[/<page=index>]", array(
      "action" => "default"
    ));
    $frontRouter[] = new Route("articles/<category>[/<page [0-9]+]", "Articles:category");
    $adminRouter = new RouteList("Admin");
    $adminRouter[] = new Route("admin/<presenter groups|users|events>", array(
      "action" => "default",
      "presenter" => array(
        Route::FILTER_TABLE => array(
          "groups" => "Group",
          "users" => "User",
          "events" => "Event"
        )
      )
    ));
    $adminRouter[] = new Route("admin/content/<presenter shop|item|job|jobMessages|town|mount|skill|adventure|adventureEnemies|itemSet>/<action>[/<id>]");
    $adminRouter[] = new Route("admin/<presenter>[/<action>][/<id>]", array(
      "presenter" => "Homepage", "action" => "default"
    ));
    $router[] = $frontRouter;
    $router[] = $adminRouter;
    $router[] = new Route("<presenter>[/<action>][/<id>]", array(
      "module" => "Front", "presenter" => "Homepage", "action" => "default"
    ));
    return $router;
  }
}
?>