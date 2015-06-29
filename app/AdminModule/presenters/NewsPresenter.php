<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI;

/**
 * Presenter News
 *
 * @author Jakub Konečný
 */
class NewsPresenter extends BasePresenter {
  /** @var \Nexendrie\News*/
  protected $model;
  
  /**
   * @param \Nexendrie\News $model
   */
  function __construct(\Nexendrie\News $model) {
    $this->model = $model;
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
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentAddNewsForm() {
    $factory = new \Nexendrie\Forms\AddEditNewsForm;
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
  function addNewsFormSucceeded(UI\Form $form, $values) {
    $this->model->user = $this->context->getService("security.user");
    $id = $this->model->add($values);
    if(is_int($id)) {
      $this->flashMessage("Novinka byla přidána.");
      $this->redirect("News:");
    }
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
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentEditNewsForm() {
    $news = $this->model->view($this->getParameter("id"));
    $factory = new \Nexendrie\Forms\AddEditNewsForm;
    $form = $factory->create();
    $form->onSuccess[] = array($this, "editNewsFormSucceeded");
    $form->setDefaults((array) $news);
    return $form;
  }
  
  /**
   * Edits news
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function editNewsFormSucceeded(UI\Form $form, $values) {
    $this->model->user = $this->context->getService("security.user");
    $this->model->edit($this->getParameter("id"), $values);
    $this->flashMessage("Novinka upravena.");
  }
  
}
?>