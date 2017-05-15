<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\AcademyControl,
    Nexendrie\Components\IAcademyControlFactory;

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
   * @param IAcademyControlFactory $factory
   * @return AcademyControl
   */
  protected function createComponentAcademy(IAcademyControlFactory $factory): AcademyControl {
    return $factory->create();
  }
}
?>