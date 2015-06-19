<?php
namespace Nexendrie\Presenters;

use Nette\Application\UI;

/**
 * Description of NewsPresenter
 *
 * @author Jakub Konečný
 */
class NewsPresenter extends BasePresenter {
  /** @var \Nexendrie\News*/
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->model = $this->context->getService("model.news");
  }
  
  function renderPage($page) {
    if($page == 1) $this->redirect("Homepage:");
    $paginator = new \Nette\Utils\Paginator;
    $this->template->news = $this->model->page($paginator, $page);
    $this->template->paginator = $paginator;
  }
  
  function renderView($id) {
    $new = $this->model->view($id);
    if(!$new) $this->forward("notfound");
    $this->template->new = $new;
  }
  
  function actionAdd() {
    $this->requiresPermissions("news", "add");
  }
  
  /**
   * Creates form for adding news
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentAddNewsForm() {
    $form = new UI\Form;
    $form->addText("title", "Titulek:")
      ->addRule(UI\Form::MAX_LENGTH, "Titulek může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titulek.");
    $form->addTextArea("text", "Text:")
      ->setRequired("Zadej text.");
    $form->addSubmit("send", "Odeslat");
    $form->onSuccess[] = array($this, "addNewsFormSucceeded");
    return $form;
  }
  
  /**
   * Adds new
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
      $this->redirect("Homepage:");
    }
  }
  
}
?>