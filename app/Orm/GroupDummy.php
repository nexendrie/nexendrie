<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * GroupDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property string $singleName
 * @property string $femaleName
 * @property int $level
 * @property string $path
 * @property int $members
 */
final class GroupDummy {
  protected int $id;
  protected string $name;
  protected string $singleName;
  protected string $femaleName;
  protected int $level;
  protected string $path;
  protected int $members;
  
  use \Nette\SmartObject;
  
  public function __construct(Group $g) {
    $this->id = $g->id;
    $this->name = $g->name;
    $this->singleName = $g->singleName;
    $this->femaleName = $g->femaleName;
    $this->level = $g->level;
    $this->path = $g->path;
    $this->members = $g->members->countStored();
  }
  
  public function getId(): int {
    return $this->id;
  }
  
  public function getName(): string {
    return $this->name;
  }
  
  public function getSingleName(): string {
    return $this->singleName;
  }
  
  public function getLevel(): int {
    return $this->level;
  }
  
  public function getPath(): string {
    return $this->path;
  }
  
  public function getMembers(): int {
    return $this->members;
  }
}
?>