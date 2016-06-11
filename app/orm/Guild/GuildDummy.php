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
 * @property int $skill
 */
class GuildDummy extends DummyEntity {
  protected $id;
  protected $name;
  protected $description;
  protected $level;
  protected $founded;
  protected $town;
  protected $money;
  protected $skill;
  
  function __construct(Guild $guild) {
    $this->id = $guild->id;
    $this->name = $guild->name;
    $this->description = $guild->description;
    $this->level = $guild->level;
    $this->founded = $guild->founded;
    $this->town = $guild->town;
    $this->money = $guild->money;
    $this->skill = (empty($guild->skill)) ? NULL : $guild->skill->id;
  }

}
?>