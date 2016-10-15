<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditAdventureFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\AdventureNotFoundException,
    Nextras\Orm\Entity\IEntity;

/**
 * Presenter Adventure
 *
 * @author Jakub Konečný
 */
class AdventurePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Adventure @autowire */
  protected $model;
  /** @var \Nexendrie\Orm\Adventure */
  private $adventure;
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /*
   * @param AddEditAdventureFormFactory $factory
   * @return Form
   */
  protected function createComponentAddAdventureForm(AddEditAdventureFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->addAdventure($values);
      $this->flashMessage("Dobrodružství přidáno.");
      $this->redirect("Content:adventures");
    };
    return $form;
  }
  
  function actionEdit(int $id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->adventure = $this->model->get($id);
    } catch(AdventureNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /*
   * @param AddEditAdventureFormFactory $factory
   * @return Form
   */
  protected function createComponentEditAdventureForm(AddEditAdventureFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->adventure->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editAdventure($this->getParameter("id"), $values);
      $this->flashMessage("Dobrodružství upraveno.");
      $this->redirect("Content:adventures");
    };
    return $form;
  }
}
?>