<?php
namespace Nexendrie\Orm;

/**
 * Description of GuildDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $level
 * @property int $founded
 * @property int $town
 * @property int $money
 * @property string $skill
 */
class GuildDummy extends DummyEntity {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var string */
  protected $description;
  /** @var int */
  protected $level;
  /** @var int */
  protected $founded;
  /** @var int */
  protected $town;
  /** @var int */
  protected $money;
  /** @var int */
  protected $skill;
  
  function __construct(Guild $guild) {
    $this->id = $guild->id;
    $this->name = $guild->name;
    $this->description = $guild->description;
    $this->level = $guild->level;
    $this->founded = $guild->founded;
    $this->town = $guild->town;
    $this->money = $guild->money;
    $this->skill = $guild->skill->id;
  }

}
?>