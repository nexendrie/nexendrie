<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditMountFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Orm\Mount as MountEntity;
use Nexendrie\Model\MountNotFoundException;

/**
 * Presenter Mount
 *
 * @author Jakub Konečný
 */
final class MountPresenter extends BasePresenter {
  protected \Nexendrie\Model\Mount $model;
  private MountEntity $mount;
  
  public function __construct(\Nexendrie\Model\Mount $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddMountForm(AddEditMountFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Jezdecké zvíře přidáno.");
      $this->redirect("Content:mounts");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->mount = $this->model->get($id);
    } catch(MountNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditMountForm(AddEditMountFormFactory $factory): Form {
    $form = $factory->create($this->mount);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Jezdecké zvíře upraveno.");
      $this->redirect("Content:mounts");
    };
    return $form;
  }
}
?>