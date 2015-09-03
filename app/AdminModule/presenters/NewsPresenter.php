<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditNewsFormFactory;

/**
 * Presenter News
 *
 * @author Jakub Konečný
 */
class NewsPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\News @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->news = $this->model->all();
  }
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("news", "add");
  }
  
  /**
   * Creates form for adding news
   * 
   * @param AddEditNewsFormFactory $factory
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentAddNewsForm(AddEditNewsFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = array($this, "addNewsFormSucceeded");
    return $form;
  }
  
  /**
   * Adds news
   * @todo redirect to the added new
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function addNewsFormSucceeded(Form $form, $values) {
    $this->model->user = $this->context->getService("security.user");
    $id = $this->model->add($values);
    $this->flashMessage("Novinka byla přidána.");
    $this->redirect("News:");
  }
  
  /**
   * Edits news
   * 
   * @param int $id News'id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("news", "edit");
    if(!$this->model->exists($id)) $this->forward("notfound");
  }
  
  /**
   * Creates form for editing news
   * 
   * @param AddEditNewsFormFactory $factory
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentEditNewsForm(AddEditNewsFormFactory $factory) {
    $news = $this->model->view($this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = array($this, "editNewsFormSucceeded");
    $form->setDefaults($news->toArray());
    return $form;
  }
  
  /**
   * Edits news
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function editNewsFormSucceeded(Form $form, $values) {
    $this->model->user = $this->context->getService("security.user");
    $this->model->edit($this->getParameter("id"), $values);
    $this->flashMessage("Novinka upravena.");
  }
  
}
?>