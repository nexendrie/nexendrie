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
final class AcademyPresenter extends BasePresenter {
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
    $this->mustNotBeTavelling();
  }
  
  protected function createComponentAcademy(IAcademyControlFactory $factory): AcademyControl {
    return $factory->create();
  }
}
?>