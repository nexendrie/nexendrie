<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Forms\AddEditMountFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Model\MountNotFoundException;

/**
 * Presenter Mount
 *
 * @author Jakub Konečný
 */
class MountPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Mount @autowire */
  protected $model;
  /** @var MountEntity */
  private $mount;
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditMountFormFactory $factory
   * @return Form
   */
  protected function createComponentAddMountForm(AddEditMountFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->add($form->getValues(true));
      $this->flashMessage("Jezdecké zvíře přidáno.");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->mount = $this->model->get($id);
    } catch(MountNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param AddEditMountFormFactory $factory
   * @return Form
   */
  protected function createComponentEditMountForm(AddEditMountFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->mount->dummyArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->edit($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Jezdecké zvíře upraveno.");
    };
    return $form;
  }
}
?>