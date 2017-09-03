<?php
declare(strict_types=1);

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
   * @throws \Nette\Application\BadRequestException
   */
  public function renderView(int $id): void {
    try {
      $this->template->article = $this->model->view($id);
    } catch(ArticleNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  protected function createComponentAddCommentForm(AddCommentFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
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
    };
    return $form;
  }
}
?>