<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Forms\AddEditAdventureFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\AdventureNotFoundException;

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
  
  /*
   * @param AddEditAdventureFormFactory $factory
   * @return Form
   */
  protected function createComponentAddAdventureForm(AddEditAdventureFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->addAdventure($form->getValues(true));
      $this->flashMessage("Dobrodružství přidáno.");
      $this->redirect("Content:adventures");
    };
    return $form;
  }
  
  function actionEdit($id) {
    try {
      $this->adventure = $this->model->get($id);
    } catch(AdventureNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /*
   * @param AddEditAdventureFormFactory $factory
   * @return Form
   */
  protected function createComponentEditAdventureForm(AddEditAdventureFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->adventure->toArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->editAdventure($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Dobrodružství upraveno.");
    };
    return $form;
  }
}
?>