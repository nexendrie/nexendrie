<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Components\AcademyControl,
    Nexendrie\Components\AcademyControlFactory;

/**
 * Presenter Academy
 *
 * @author Jakub Konečný
 */
class AcademyPresenter extends BasePresenter {
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @param AcademyControlFactory $factory
   * @return AcademyControl
   */
  protected function createComponentAcademy(AcademyControlFactory $factory) {
    return $factory->create();
  }
}
?>