<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\AdventureNotFoundException,
    Nexendrie\Orm\Adventure as AdventureEntity,
    Nexendrie\Forms\AddEditAdventureEnemyFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\AdventureNpcNotFoundException,
    Nexendrie\Orm\AdventureNpc as AdventureNpcEntity,
    Nextras\Orm\Entity\IEntity;

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
   * @throws \Nette\Application\BadRequestException
   */
  function actionList(int $id): void {
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
   * @throws \Nette\Application\BadRequestException
   */
  function actionAdd(int $id): void {
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
  protected function createComponentAddAdventureEnemyForm(AddEditAdventureEnemyFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $data) {
      $data["adventure"] = $this->adventure->id;
      $this->model->addNpc($data);
      $this->flashMessage("Nepřítel přidán.");
      $this->redirect("list", ["id" => $this->adventure->id]);
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->npc = $this->model->getNpc($id);
    } catch (AdventureNpcNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditAdventureEnemyFormFactory $factory
   * @return Form
   */
  protected function createComponentEditAdventureEnemyForm(AddEditAdventureEnemyFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->npc->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editNpc($this->getParameter("id"), $values);
      $this->flashMessage("Nepřítel upraven.");
      $this->redirect("list", ["id" => $this->npc->adventure->id]);
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionDelete(int $id): void {
    try {
      $adventure = $this->model->deleteNpc($id);
      $this->flashMessage("Nepřítel smazán.");
      $this->redirect("list", ["id" => $adventure]);
    } catch(AdventureNpcNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>