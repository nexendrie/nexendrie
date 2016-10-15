<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

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
    $this->mustNotBeTavelling();
  }
  
  /**
   * @param AcademyControlFactory $factory
   * @return AcademyControl
   */
  protected function createComponentAcademy(AcademyControlFactory $factory): AcademyControl {
    return $factory->create();
  }
}
?>