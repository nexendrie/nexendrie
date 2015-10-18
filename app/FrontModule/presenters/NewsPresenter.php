<?php
namespace Nexendrie\FrontModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddCommentFormFactory,
    Nexendrie\Model\NewsNotFoundException,
    Nexendrie\Model\AuthenticationNeededException,
    Nexendrie\Model\MissingPermissionsException;

/**
 * Description of NewsPresenter
 *
 * @author Jakub Konečný
 */
class NewsPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\News @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function renderPage($page) {
    if($page == 1) $this->redirect("Homepage:");
    $paginator = new \Nette\Utils\Paginator;
    $this->template->news = $this->model->page($paginator, $page);
    $this->template->paginator = $paginator;
  }
  
  /**
   * @return void
   */
  function renderView($id) {
    try {
      $this->template->news = $this->model->view($id);
    } catch(NewsNotFoundException $e) {
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
   * @param \Nette\Application\UI\Form $form
   */
  function addCommentFormSucceeded(Form $form, array $values) {
    $values["news"] = $this->getParameter("id");
    try {
      $this->model->user = $this->user;
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