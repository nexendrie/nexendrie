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
  /** @var \Nexendrie\Model\Town */
  protected $model;
  /** @var TownEntity */
  private $town;
  
  public function __construct(\Nexendrie\Model\Town $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddTownForm(AddEditTownFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->add($values);
      $this->flashMessage("Město přidáno.");
      $this->redirect("Content:towns");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->town = $this->model->get($id);
    } catch(TownNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  protected function createComponentEditTownForm(AddEditTownFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->town->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->edit((int) $this->getParameter("id"), $values);
      $this->flashMessage("Město upraveno.");
      $this->redirect("Content:towns");
    };
    return $form;
  }
}
?>