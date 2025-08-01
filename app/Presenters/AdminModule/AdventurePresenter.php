<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditAdventureFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\Adventure;
use Nexendrie\Model\AdventureNotFoundException;

/**
 * Presenter Adventure
 *
 * @author Jakub Konečný
 */
final class AdventurePresenter extends BasePresenter {
  private \Nexendrie\Orm\Adventure $adventure;
  
  public function __construct(private readonly Adventure $model) {
    parent::__construct();
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddAdventureForm(AddEditAdventureFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Dobrodružství přidáno.");
      $this->redirect("Content:adventures");
    };
    return $form;
  }

  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->adventure = $this->model->get($id);
    } catch(AdventureNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditAdventureForm(AddEditAdventureFormFactory $factory): Form {
    $form = $factory->create($this->adventure);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Dobrodružství upraveno.");
      $this->redirect("Content:adventures");
    };
    return $form;
  }
}
?>