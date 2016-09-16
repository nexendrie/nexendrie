<?php
namespace Nexendrie\Orm;

/**
 * PermissionDummy
 *
 * @author Jakub Konečný
 * @property-read int $id
 * @property-read string $resource
 * @property-read string $action
 * @property-read int $group
 */
class PermissionDummy {
  /** @var int */
  protected $id;
  /** @var string */
  protected $resource;
  /** @var string */
  protected $action;
  /** @var int */
  protected $group;
  
  use \Nette\SmartObject;
  
  function __construct(Permission $p) {
    $this->id = $p->id;
    $this->resource = $p->resource;
    $this->action = $p->action;
    $this->group = $p->group->id;
  }
  
  function getId() {
    return $this->id;
  }
  
  function getResource() {
    return $this->resource;
  }
  
  function getAction() {
    return $this->action;
  }
  
  function getGroup() {
    return $this->group;
  }
}
?>