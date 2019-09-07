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
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nexendrie\Model\OpenSearch */
  protected $openSearch;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\OpenSearch $openSearch) {
    parent::__construct();
    $this->orm = $orm;
    $this->openSearch = $openSearch;
  }
  
  protected function createComponentSiteSearchForm(SiteSearchFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $text = $values["text"];
      switch($values["type"]) {
        case SiteSearchFormFactory::TYPE_USERS:
          $this->template->results = $this->orm->users->findByLikeName($text);
          break;
        case SiteSearchFormFactory::TYPE_ARTICLES_TITLES:
          $this->template->results = $this->orm->articles->findByLikeTitle($text);
          break;
        case SiteSearchFormFactory::TYPE_ARTICLES_TEXTS:
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

  public function actionArticlesTitle(): void {
    $description = $this->openSearch->createDescription("Články 1", "Titulky článků", "Hledat v titulcích článků", "články titulek", "articlesTitles");
    $this->sendOpenSearchDescriptionResponse($description);
  }

  public function actionArticlesText(): void {
    $description = $this->openSearch->createDescription("Články 2", "Texty článků", "Hledat v textech článků", "články text", "articlesTexts");
    $this->sendOpenSearchDescriptionResponse($description);
  }
}
?>