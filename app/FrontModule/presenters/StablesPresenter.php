<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Model\MountNotFoundException,
    Nexendrie\Forms\ManageMountFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Components\StablesControlFactory,
    Nexendrie\Components\StablesControl;



/**
 * Presenter Stables
 *
 * @author Jakub Konečný
 */
class StablesPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Mount @autowire */
  protected $model;
  /** @var MountEntity */
  private $mount;
  
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
   * @return void
   */
  function renderDefault() {
    $this->template->mounts = $this->model->listOfMounts($this->user->id);
  }
  
  /**
   * @param StablesControlFactory $factory
   * @return StablesControl
   */
  protected function createComponentStables(StablesControlFactory $factory): StablesControl {
    return $factory->create();
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionManage(int $id) {
    try {
      $this->mount = $this->model->get($id);
    } catch(MountNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
    if($this->mount->owner->id != $this->user->id) throw new \Nette\Application\BadRequestException;
  }
  
  /**
   * @param ManageMountFormFactory $factory
   * @return Form
   */
  protected function createComponentManageMountForm(ManageMountFormFactory $factory): Form {
    $form = $factory->create($this->mount->id);
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionTrain(int $id) {
    try {
      $mount = $this->model->get($id);
    } catch(MountNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
    if($mount->owner->id != $this->user->id) throw new \Nette\Application\BadRequestException;
    $this->template->mountId = $id;
  }
}
?>