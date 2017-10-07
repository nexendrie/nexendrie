<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Orm\Marriage as MarriageEntity;

/**
 * WeddingControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template|\stdClass $template
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
  
  public function __construct(\Nexendrie\Model\Marriage $model, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  public function setMarriage(MarriageEntity $marriage) {
    $this->marriage = $marriage;
  }
  
  /**
   * @return string[]
   */
  protected function getTexts(): array {
    $texts = [];
    return $texts;
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/wedding.latte");
    $this->template->marriage = $this->marriage;
    $this->template->texts = $this->getTexts();
    $this->template->render();
  }
}
?>