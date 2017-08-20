<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditArticleFormFactory,
    Nexendrie\Model\ArticleNotFoundException;

/**
 * Presenter Article
 *
 * @author Jakub Konečný
 */
class ArticlePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article @autowire */
  protected $model;
  
  public function renderDefault(): void {
    $this->template->articles = $this->model->listOfArticles();
  }
  
  public function actionAdd(): void {
    $this->requiresPermissions("article", "add");
  }
  
  protected function createComponentAddArticleForm(AddEditArticleFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $id = $this->model->addArticle($values);
      $this->flashMessage("Článek byla přidán.");
      $this->redirect(":Front:Article:", ["id" => $id]);
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    try {
      $article = $this->model->view($id);
    } catch(ArticleNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
    if($article->author->id != $this->user->id) {
      $this->requiresPermissions("article", "edit");
    }
  }
  
  protected function createComponentEditArticleForm(AddEditArticleFormFactory $factory): Form {
    $news = $this->model->view($this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editArticle($this->getParameter("id"), $values);
      $this->flashMessage("Článek upraven.");
    };
    $form->setDefaults($news->toArray());
    return $form;
  }
}
?>