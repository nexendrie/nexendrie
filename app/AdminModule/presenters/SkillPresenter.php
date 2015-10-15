<?php
namespace Nexendrie\AdminModule\Presenters;

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
   * @param AddEditSkillFormFactory $factory
   * @return Form
   */
  protected function createComponentAddSkillForm(AddEditSkillFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->add($form->getValues(true));
      $this->flashMessage("Dovednost přidána.");
      $this->redirect("Content:skills");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    try {
      $this->skill = $this->model->get($id);
    } catch(SkillNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param AddEditSkillFormFactory $factory
   * @return Form
   */
  protected function createComponentEditSkillForm(AddEditSkillFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->skill->toArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->edit($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Dovednost upravena.");
    };
    return $form;
  }
}
?>