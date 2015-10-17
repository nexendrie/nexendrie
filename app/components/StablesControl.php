<?php
namespace Nexendrie\Components;

/**
 * StablesControl
 *
 * @author Jakub Konečný
 */
class StablesControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/stables.latte");
    $template->mounts = $this->orm->mounts->findByOwner($this->user->id);
    $template->render();
  }
}

interface StablesControlFactory {
  /** @return StablesControl */
  function create();
}
?>