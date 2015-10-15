<?php
namespace Nexendrie\Orm;

/**
 * GroupDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property string $singleName
 * @property int $level
 * @property string $path
 * @property int $members
 */
class GroupDummy extends \Nette\Object {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var string */
  protected $singleName;
  /** @var int */
  protected $level;
  /** @var string */
  protected $path;
  /** @var int */
  protected $members;
  
  function __construct(Group $g) {
    $this->id = $g->id;
    $this->name = $g->name;
    $this->singleName = $g->singleName;
    $this->level = $g->level;
    $this->path = $g->path;
    $this->members = $g->members->count();
  }
  
  function getId() {
    return $this->id;
  }
  
  function getName() {
    return $this->name;
  }
  
  function getSingleName() {
    return $this->singleName;
  }
  
  function getLevel() {
    return $this->level;
  }
  
  function getPath() {
    return $this->path;
  }
  
  function getMembers() {
    return $this->members;
  }
}
?>