<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * GroupDummy
 *
 * @author Jakub Konečný
 */
final class GroupDummy {
  public readonly int $id;
  public readonly string $name;
  public readonly string $singleName;
  public readonly string $femaleName;
  public readonly int $level;
  public readonly string $path;
  public readonly int $members;
  
  public function __construct(Group $g) {
    $this->id = $g->id;
    $this->name = $g->name;
    $this->singleName = $g->singleName;
    $this->femaleName = $g->femaleName;
    $this->level = $g->level;
    $this->path = $g->path;
    $this->members = $g->members->countStored();
  }
}
?>