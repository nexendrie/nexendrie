<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\ArticleNotFoundException,
    Nette\Application\UI\Form,
    Nexendrie\Forms\AddCommentFormFactory,
    Nexendrie\Model\AuthenticationNeededException,
    Nexendrie\Model\MissingPermissionsException;

/**
 * Presenter Article
 *
 * @author Jakub Konečný
 */
class ArticlePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article @autowire */
  protected $model;
  
  /**
   * @param int $id
   * @return void
   */
  function renderView($id) {
    try {
      $this->template->article = $this->model->view($id);
    } catch(ArticleNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * Creates form for adding comment to news
   * @param AddCommentFormFactory $factory
   * @return Form
   */
  protected function createComponentAddCommentForm(AddCommentFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = array($this, "addCommentFormSucceeded");
    return $form;
  }
  
  /**
   * Adds comment to news
   * 
   * @param Form $form
   */
  function addCommentFormSucceeded(Form $form) {
    $values = $form->getValues(true);
    $values["article"] = $this->getParameter("id");
    try {
      $this->model->addComment($values);
      $this->flashMessage("Komentář přidán.");
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("Pro přidání komentáře musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException $e) {
      $this->flashMessage("Nemůžeš přidávat komentáře.");
    }
  }
}
?>