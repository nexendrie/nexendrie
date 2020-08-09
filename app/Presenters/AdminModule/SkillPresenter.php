<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Orm\Skill as SkillEntity;
use Nexendrie\Forms\AddEditSkillFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\SkillNotFoundException;

/**
 * Presenter Skills
 *
 * @author Jakub Konečný
 */
final class SkillPresenter extends BasePresenter {
  protected \Nexendrie\Model\Skills $model;
  private SkillEntity $skill;
  
  public function __construct(\Nexendrie\Model\Skills $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddSkillForm(AddEditSkillFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Dovednost přidána.");
      $this->redirect("Content:skills");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->skill = $this->model->get($id);
    } catch(SkillNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditSkillForm(AddEditSkillFormFactory $factory): Form {
    $form = $factory->create($this->skill);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Dovednost upravena.");
      $this->redirect("Content:skills");
    };
    return $form;
  }
}
?>