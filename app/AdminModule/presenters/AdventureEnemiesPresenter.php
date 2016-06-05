<?php
namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\AdventureNotFoundException,
    Nexendrie\Orm\Adventure as AdventureEntity,
    Nexendrie\Forms\AddEditAdventureEnemyFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\AdventureNpcNotFoundException,
    Nexendrie\Orm\AdventureNpc as AdventureNpcEntity;

/**
 * Presenter AdventureEnemy
 *
 * @author Jakub Konečný
 */
class AdventureEnemiesPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Adventure @autowire */
  protected $model;
  /** @var AdventureEntity */
  private $adventure;
  /** @var AdventureNpcEntity */
  private $npc;
  
  /**
   * @param int $id
   * @return void
   */
  function actionList($id) {
    $this->requiresPermissions("content", "list");
    try {
      $this->template->npcs = $this->model->listOfNpcs($id);
      $this->template->adventureId = $id;
    } catch(AdventureNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  /**
   * @param int $id
   * @return void
   */
  function actionAdd($id) {
    $this->requiresPermissions("content", "add");
    try {
      $this->adventure = $this->model->get($id);
      $this->template->adventureName = $this->adventure->name;
    } catch(AdventureNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditAdventureEnemyFormFactory $factory
   * @return Form
   */
  protected function createComponentAddAdventureEnemyForm(AddEditAdventureEnemyFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $data = $form->getValues(true);
      $data["adventure"] = $this->adventure->id;
      $this->model->addNpc($data);
      $this->flashMessage("Nepřítel přidán.");
      $this->redirect("list", array("id" => $this->adventure->id));
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
      $this->npc = $this->model->getNpc($id);
    } catch (AdventureNpcNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  protected function createComponentEditAdventureEnemyForm(AddEditAdventureEnemyFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->npc->dummyArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->editNpc($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Nepřítel upraven.");
      $this->redirect("list", array("id" => $this->npc->adventure->id));
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionDelete($id) {
    try {
      $adventure = $this->model->deleteNpc($id);
      $this->flashMessage("Nepřítel smazán.");
      $this->redirect("list", array("id" => $adventure));
    } catch(AdventureNpcNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>