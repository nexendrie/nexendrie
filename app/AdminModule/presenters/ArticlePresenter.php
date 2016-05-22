<?php
namespace Nexendrie\Presenters\AdminModule;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditArticleFormFactory,
    Nexendrie\Model\ArticleNotFoundException;

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
    $this->template->articles = $this->model->listOfArticles();
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
      $this->model->addArticle($form->getValues(true));
      $this->flashMessage("Novinka byla přidána.");
      $this->redirect("Article:");
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
    try {
      $article = $this->model->view($id);
    } catch(ArticleNotFoundException $e) {
      $this->forward("notfound");
    }
    if($article->author->id != $this->user->id) $this->requiresPermissions("article", "edit");
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