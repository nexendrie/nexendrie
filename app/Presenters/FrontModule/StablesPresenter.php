<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Orm\Mount as MountEntity;
use Nexendrie\Model\MountNotFoundException;
use Nexendrie\Forms\ManageMountFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Components\IStablesControlFactory;
use Nexendrie\Components\StablesControl;

/**
 * Presenter Stables
 *
 * @author Jakub Konečný
 */
final class StablesPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Mount */
  protected $model;
  /** @var MountEntity */
  private $mount;
  
  public function __construct(\Nexendrie\Model\Mount $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
    $this->mustNotBeTavelling();
  }
  
  public function renderDefault(): void {
    $this->template->mounts = $this->model->listOfMounts($this->user->id);
  }
  
  protected function createComponentStables(IStablesControlFactory $factory): StablesControl {
    return $factory->create();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionManage(int $id): void {
    try {
      $this->mount = $this->model->get($id);
    } catch(MountNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
    if($this->mount->owner->id !== $this->user->id) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentManageMountForm(ManageMountFormFactory $factory): Form {
    $form = $factory->create($this->mount->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionTrain(int $id): void {
    try {
      $mount = $this->model->get($id);
    } catch(MountNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
    if($mount->owner->id !== $this->user->id) {
      throw new \Nette\Application\BadRequestException();
    }
    $this->template->mountId = $id;
  }
}
?>