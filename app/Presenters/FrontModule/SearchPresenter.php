<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\SiteSearchFormFactory;
use Nexendrie\Model\OpenSearchDescriptionResponse;

/**
 * SearchPresenter
 *
 * @author Jakub Konečný
 */
final class SearchPresenter extends BasePresenter {
  protected \Nexendrie\Orm\Model $orm;
  protected \Nexendrie\Model\OpenSearch $openSearch;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\OpenSearch $openSearch) {
    parent::__construct();
    $this->orm = $orm;
    $this->openSearch = $openSearch;
  }
  
  protected function createComponentSiteSearchForm(SiteSearchFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values): void {
      $text = $values["text"];
      switch($values["type"]) {
        case SiteSearchFormFactory::TYPE_USERS:
          $this->template->results = $this->orm->users->findByLikeName($text);
          break;
        case SiteSearchFormFactory::TYPE_ARTICLES:
          $this->template->results = $this->orm->articles->findByText($text);
          break;
        default:
          return;
      }
    };
    return $form;
  }

  protected function sendOpenSearchDescriptionResponse(string $description): void {
    $this->sendResponse(new OpenSearchDescriptionResponse($description));
  }

  public function actionUsers(): void {
    $description = $this->openSearch->createDescription("Uživatelé", "Uživatelé", "Hledat v uživatelích", "uživatelé", "users");
    $this->sendOpenSearchDescriptionResponse($description);
  }

  public function actionArticles(): void {
    $description = $this->openSearch->createDescription("Články", "Články", "Hledat v článcích", "články", "articles");
    $this->sendOpenSearchDescriptionResponse($description);
  }

  protected function getDataModifiedTime(): int {
    if(isset($this->template->results)) {
      return time();
    }
    return 0;
  }
}
?>