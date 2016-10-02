<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditTownFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\Town as TownEntity,
    Nexendrie\Model\TownNotFoundException,
    Nextras\Orm\Entity\IEntity;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
class TownPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Town @autowire */
  protected $model;
  /** @var TownEntity */
  private $town;
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditTownFormFactory $factory
   * @return Form
   */
  protected function createComponentAddTownForm(AddEditTownFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $values = $form->getValues(true);
      $this->model->add($values);
      $this->flashMessage("Město přidáno.");
      $this->redirect("Content:towns");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit($id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->town = $this->model->get($id);
    } catch(TownNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditTownFormFactory $factory
   * @return Form
   */
  protected function createComponentEditTownForm(AddEditTownFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->town->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form) {
      $this->model->edit($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Město upraveno.");
      $this->redirect("Content:towns");
    };
    return $form;
  }
}
?>