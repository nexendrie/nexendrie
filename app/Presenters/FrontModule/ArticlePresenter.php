<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\ArticleNotFoundException;
use Nette\Application\UI\Form;
use Nexendrie\Forms\AddCommentFormFactory;
use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\MissingPermissionsException;

/**
 * Presenter Article
 *
 * @author Jakub Konečný
 */
final class ArticlePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Article $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderView(int $id): void {
    try {
      $this->template->article = $this->model->view($id);
      $this->template->ogType = "article";
    } catch(ArticleNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentAddCommentForm(AddCommentFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $values["article"] = (int) $this->getParameter("id");
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