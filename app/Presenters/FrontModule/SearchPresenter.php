<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\SiteSearchFormFactory;
use Nexendrie\Model\OpenSearch;
use Nexendrie\Model\OpenSearchDescriptionResponse;
use Nexendrie\Orm\Model as ORM;

/**
 * SearchPresenter
 *
 * @author Jakub Konečný
 */
final class SearchPresenter extends BasePresenter
{
    protected bool $earlyHints = false;

    public function __construct(private readonly ORM $orm, private readonly OpenSearch $openSearch)
    {
        parent::__construct();
    }

    protected function createComponentSiteSearchForm(SiteSearchFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (Form $form, array $values): void {
            $text = $values["text"];
            switch ($values["type"]) {
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

    protected function sendOpenSearchDescriptionResponse(string $description): never
    {
        $this->sendResponse(new OpenSearchDescriptionResponse($description));
    }

    public function actionUsers(): void
    {
        $description = $this->openSearch->createDescription("Uživatelé", "Uživatelé", "Hledat v uživatelích", "uživatelé", "users");
        $this->sendOpenSearchDescriptionResponse($description);
    }

    public function actionArticles(): void
    {
        $description = $this->openSearch->createDescription("Články", "Články", "Hledat v článcích", "články", "articles");
        $this->sendOpenSearchDescriptionResponse($description);
    }

    protected function getDataModifiedTime(): int
    {
        if (isset($this->template->results)) {
            return time();
        }
        return 0;
    }
}
