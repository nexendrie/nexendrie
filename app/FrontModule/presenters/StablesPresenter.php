<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Model\MountNotFoundException,
    Nexendrie\Forms\ManageMountFormFactory,
    Nette\Application\UI\Form;



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
  function startup() {
    parent::startup();
    $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->mounts = $this->model->listOfMounts($this->user->id);
  }
  
  /**
   * @return void
   */
  function actionManage($id) {
    try {
      $this->mount = $this->model->get($id);
    } catch(MountNotFoundException $e) {
      $this->forward("notfound");
    }
    if($this->mount->owner->id != $this->user->id) $this->forward("notfound");
  }
  
  /**
   * @param ManageMountFormFactory $factory
   * @return Form
   */
  protected function createComponentManageMountForm(ManageMountFormFactory $factory) {
    $form = $factory->create($this->mount->id);
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>