<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Nexendrie\RestRoute\RestRoute;

/**
 * Router Factory
 *
 * @author Jakub Konečný
 */
final class RouterFactory
{
    public function create(): RouteList
    {
        $router = new RouteList();
        $restRoute = new RestRoute("Api");
        $restRoute->useURLModuleVersioning(RestRoute::MODULE_VERSION_PATH_PREFIX_PATTERN, [
            null => "V1",
            "v1" => "V1",
        ]);
        $router->add($restRoute);
        $frontRouter = new RouteList("Front");
        $frontRouter->add(new Route("/", "Homepage:page"));
        $frontRouter->add(new Route("profile/<name>[/<action>]", "Profile:default"));
        $frontRouter->add(new Route("<presenter message|poll|article|event>/<id [0-9]+>", [
            "action" => "view",
            "presenter" => [
                Route::FilterTable => [
                    "message" => "Messages"
                ]
            ]
        ]));
        $frontRouter->add(new Route("<presenter order|town|guild|monastery|castle>/<id [0-9]+>", [
            "action" => "detail",
        ]));
        $frontRouter->add(new Route("page/<page [0-9]+>", "Homepage:page"));
        $frontRouter->add(new Route("rss[/<action>][/<id [0-9]+>]", "Rss:news"));
        $frontRouter->add(new Route("<presenter help|history>[/<page=index>]", [
            "action" => "default"
        ]));
        $frontRouter->add(new Route("articles/<category>[/<page [0-9]+>]", "Articles:category"));
        $adminRouter = new RouteList("Admin");
        $adminRouter->add(new Route("admin/<presenter groups|users|events>", [
            "action" => "default",
            "presenter" => [
                Route::FilterTable => [
                    "groups" => "Group",
                    "users" => "User",
                    "events" => "Event"
                ]
            ]
        ]));
        $adminRouter->add(new Route("admin/content/<presenter shop|item|job|jobMessages|town|mount|skill|adventure|adventureEnemies|itemSet>/<action>[/<id>]"));
        $adminRouter->add(new Route("admin/<presenter>[/<action>][/<id>]", [
            "presenter" => "Homepage", "action" => "default"
        ]));
        $router->add($frontRouter);
        $router[] = $adminRouter;
        $router->add(new Route("<presenter>[/<action>][/<id>]", [
            "module" => "Front", "presenter" => "Homepage", "action" => "default"
        ]));
        return $router;
    }
}
