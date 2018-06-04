<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

/**
 * Router Factory
 *
 * @author Jakub Konečný
 */
final class RouterFactory {
  use \Nette\SmartObject;
  
  public function create(): RouteList {
    $router = new RouteList();
    $frontRouter = new RouteList("Front");
    $frontRouter[] = new Route("/", "Homepage:page");
    $frontRouter[] = new Route("profile[/<action>]/<username>", "Profile:default");
    $frontRouter[] = new Route("<presenter message|poll|article|event>/<id [0-9]+>", [
      "action" => "view",
      "presenter" => [
        Route::FILTER_TABLE => [
          "message" => "Messages"
        ]
      ] 
    ]);
    $frontRouter[] = new Route("page/<page [0-9]+>", "Homepage:page");
    $frontRouter[] = new Route("rss[/<action>][/<news [0-9]+>]", "Rss:news");
    $frontRouter[] = new Route("<presenter help|history>[/<page=index>]", [
      "action" => "default"
    ]);
    $frontRouter[] = new Route("articles/<category>[/<page [0-9]+>]", "Articles:category");
    $adminRouter = new RouteList("Admin");
    $adminRouter[] = new Route("admin/<presenter groups|users|events>", [
      "action" => "default",
      "presenter" => [
        Route::FILTER_TABLE => [
          "groups" => "Group",
          "users" => "User",
          "events" => "Event"
        ]
      ]
    ]);
    $adminRouter[] = new Route("admin/content/<presenter shop|item|job|jobMessages|town|mount|skill|adventure|adventureEnemies|itemSet>/<action>[/<id>]");
    $adminRouter[] = new Route("admin/<presenter>[/<action>][/<id>]", [
      "presenter" => "Homepage", "action" => "default"
    ]);
    $router[] = $frontRouter;
    $router[] = $adminRouter;
    $router[] = new Route("<presenter>[/<action>][/<id>]", [
      "module" => "Front", "presenter" => "Homepage", "action" => "default"
    ]);
    return $router;
  }
}
?>