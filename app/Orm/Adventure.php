<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Adventure
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property string $intro
 * @property string $epilogue
 * @property int $reward
 * @property int $level {default 55}
 * @property Event|null $event {m:1 Event::$adventures}
 * @property int $created
 * @property int $updated
 * @property OneHasMany|AdventureNpc[] $npcs {1:m AdventureNpc::$adventure, orderBy=order}
 * @property OneHasMany|UserAdventure[] $userAdventures {1:m UserAdventure::$adventure}
 */
final class Adventure extends BaseEntity {

}
?>