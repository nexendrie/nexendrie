<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Orm\Skill as SkillEntity,
    Nexendrie\Forms\AddEditSkillFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\SkillNotFoundException;

/**
 * Presenter Skills
 *
 * @author Jakub Konečný
 */
class SkillPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Skills @autowire */
  protected $model;
  /** @var SkillEntity */
  private $skill;
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditSkillFormFactory $factory
   * @return Form
   */
  protected function createComponentAddSkillForm(AddEditSkillFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->add($values);
      $this->flashMessage("Dovednost přidána.");
      $this->redirect("Content:skills");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->skill = $this->model->get($id);
    } catch(SkillNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditSkillFormFactory $factory
   * @return Form
   */
  protected function createComponentEditSkillForm(AddEditSkillFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->skill->toArray());
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->edit($this->getParameter("id"), $values);
      $this->flashMessage("Dovednost upravena.");
      $this->redirect("Content:skills");
    };
    return $form;
  }
}
?>