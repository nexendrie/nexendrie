<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\ISharerControlFactory;
use Nexendrie\Components\SharerControl;
use Nexendrie\Model\Article;
use Nexendrie\Model\ArticleNotFoundException;
use Nette\Application\UI\Form;
use Nexendrie\Forms\AddCommentFormFactory;
use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\CommentNotFoundException;
use Nexendrie\Model\ContentAlreadyReportedException;
use Nexendrie\Model\MissingPermissionsException;
use Nexendrie\Model\Moderation;

/**
 * Presenter Article
 *
 * @author Jakub Konečný
 */
final class ArticlePresenter extends BasePresenter {
  protected bool $publicCache = false;
  
  public function __construct(private readonly Article $model, private readonly Moderation $moderationModel) {
    parent::__construct();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderView(int $id): void {
    try {
      $this->template->article = $article = $this->model->view($id);
      $this->template->comments = $article->comments->toCollection()->findBy(["deleted" => false]);
      $this->template->ogType = "article";
      $this->template->link = $this->link("//this");
    } catch(ArticleNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentAddCommentForm(AddCommentFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values): void {
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

  public function createComponentSharer(ISharerControlFactory $factory): SharerControl {
    return $factory->create();
  }

  public function handleReport(int $comment): void {
    try {
      $this->moderationModel->reportComment($comment);
      $this->flashMessage("Komentář nahlášen.");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("Pro nahlášení komentáře musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(CommentNotFoundException) {
      $this->flashMessage("Komentář nenalezen.");
      $this->redirect("Homepage:");
    } catch(ContentAlreadyReportedException) {
      $this->flashMessage("Tento komentář je již nahlášený.");
    }
    $this->redirect("this");
  }

  protected function getDataModifiedTime(): int {
    $time = $this->template->article->updated;
    /** @var \Nexendrie\Orm\Comment $comment */
    foreach($this->template->article->comments as $comment) {
      $time = max($time, $comment->created);
    }
    return $time;
  }
}
?>