<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditTownFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\Town;
use Nexendrie\Orm\Town as TownEntity;
use Nexendrie\Model\TownNotFoundException;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
final class TownPresenter extends BasePresenter {
  private TownEntity $town;
  
  public function __construct(private readonly Town $model) {
    parent::__construct();
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddTownForm(AddEditTownFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
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
    } catch(TownNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditTownForm(AddEditTownFormFactory $factory): Form {
    $form = $factory->create($this->town);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Město upraveno.");
      $this->redirect("Content:towns");
    };
    return $form;
  }
}
?>