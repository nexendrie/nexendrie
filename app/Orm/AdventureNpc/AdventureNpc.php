<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * AdventureNpc
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property Adventure $adventure {m:1 Adventure::$npcs}
 * @property int $order
 * @property int $hitpoints 
 * @property int $strength
 * @property int $armor
 * @property int $initiative {default 0}
 * @property int $reward
 * @property string $encounterText
 * @property string $victoryText
 */
final class AdventureNpc extends BaseEntity {
  
}
?>