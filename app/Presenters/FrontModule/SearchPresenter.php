<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\SiteSearchFormFactory;

/**
 * SearchPresenter
 *
 * @author Jakub Konečný
 */
final class SearchPresenter extends BasePresenter {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    parent::__construct();
    $this->orm = $orm;
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
}
?>