<?php
namespace Nexendrie\Components;

/**
 * AdventureControl
 *
 * @author Jakub Konečný
 */
class AdventureControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Adventure */
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Adventure $model, \Nette\Security\User $user) {
    $this->model = $model;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function renderList() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/adventureList.latte");
    $template->adventures = $this->model->findAvailableAdventures();
    $template->render();
  }
}

interface AdventureControlFactory {
  /** @return AdventureControl */
  function create();
}
?>