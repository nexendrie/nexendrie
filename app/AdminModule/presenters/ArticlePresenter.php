<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditArticleFormFactory;

/**
 * Presenter News
 *
 * @author Jakub Konečný
 */
class ArticlePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->news = $this->model->listOfNews();
  }
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("article", "add");
  }
  
  /**
   * Creates form for adding news
   * @todo redirect to the added news
   * 
   * @param AddEditArticleFormFactory $factory
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentAddArticleForm(AddEditArticleFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->addNews($form->getValues(true));
      $this->flashMessage("Novinka byla přidána.");
      $this->redirect("News:");
    };
    return $form;
  }
  
  /**
   * Edits news
   * 
   * @param int $id News'id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("article", "edit");
    if(!$this->model->exists($id)) $this->forward("notfound");
  }
  
  /**
   * Creates form for editing news
   * 
   * @param AddEditArticleFormFactory $factory
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentEditArticleForm(AddEditArticleFormFactory $factory) {
    $news = $this->model->view($this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->editArticle($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Novinka upravena.");
    };
    $form->setDefaults($news->toArray());
    return $form;
  }
}
?>