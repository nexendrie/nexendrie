<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Orm\Marriage as MarriageEntity;

/**
 * WeddingControl
 *
 * @author Jakub Konečný
 * @property-write MarriageEntity $marriage
 */
class WeddingControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Marriage */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var MarriageEntity */
  protected $marriage;
  
  function __construct(\Nexendrie\Model\Marriage $model, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  function setMarriage(MarriageEntity $marriage) {
    $this->marriage = $marriage;
  }
  
  /**
   * @return string[]
   */
  protected function getTexts(): array {
    $texts = [];
    return $texts;
  }
  
  /**
   * @return void
   */
  function render() {
    $this->template->setFile(__DIR__ . "/wedding.latte");
    $this->template->marriage = $this->marriage;
    $this->template->texts = $this->getTexts();
    $this->template->render();
  }
}

interface WeddingControlFactory {
  /** @return WeddingControl */
  function create();
}
?>