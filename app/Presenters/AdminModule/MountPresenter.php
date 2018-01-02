<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditMountFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Model\MountNotFoundException,
    Nextras\Orm\Entity\IEntity;

/**
 * Presenter Mount
 *
 * @author Jakub Konečný
 */
class MountPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Mount */
  protected $model;
  /** @var MountEntity */
  private $mount;
  
  public function __construct(\Nexendrie\Model\Mount $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddMountForm(AddEditMountFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->add($values);
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
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  protected function createComponentEditMountForm(AddEditMountFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->mount->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->edit($this->getParameter("id"), $values);
      $this->flashMessage("Jezdecké zvíře upraveno.");
      $this->redirect("Content:mounts");
    };
    return $form;
  }
}
?>