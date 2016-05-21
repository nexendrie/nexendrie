<?php
namespace Nexendrie\Orm;

/**
 * AdventureDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $intro
 * @property string $epilogue
 * @property int $reward
 * @property int $level
 * @property int|NULL $event
 */
class AdventureDummy extends DummyEntity {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var string */
  protected $description;
  /** @var string */
  protected $intro;
  /** @var string */
  protected $epilogue;
  /** @var int */
  protected $reward;
  /** @var int */
  protected $level;
  /** @var int|NULL */
  protected $event;
  
  function __construct(Adventure $adventure) {
    $this->id = $adventure->id;
    $this->name = $adventure->name;
    $this->description = $adventure->description;
    $this->intro = $adventure->intro;
    $this->epilogue = $adventure->epilogue;
    $this->reward = $adventure->reward;
    $this->level = $adventure->level;
    $this->event = (empty($adventure->event)) ? NULL : $adventure->event->id;
  }
}
?>