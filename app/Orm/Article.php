<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Article
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $title
 * @property string $text
 * @property User $author {m:1 User::$articles}
 * @property string $category {enum static::CATEGORY_*}
 * @property int $created
 * @property int $updated
 * @property-read string $createdAt {virtual}
 * @property-read string $updatedAt {virtual}
 * @property bool $allowedComments {default true}
 * @property OneHasMany|Comment[] $comments {1:m Comment::$article}
 * @property-read string $categoryCZ {virtual}
 * @property-read int $commentsCount {virtual}
 */
final class Article extends BaseEntity
{
    public const string CATEGORY_NEWS = "news";
    public const string CATEGORY_CHRONICLE = "chronicle";
    public const string CATEGORY_POETRY = "poetry";
    public const string CATEGORY_SHORT_STORY = "short_story";
    public const string CATEGORY_ESSAY = "essay";
    public const string CATEGORY_NOVELLA = "novella";
    public const string CATEGORY_FAIRY_TALE = "fairy_tale";
    public const string CATEGORY_UNCATEGORIZED = "uncategorized";

    private \Nexendrie\Model\Locale $localeModel;

    public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void
    {
        $this->localeModel = $localeModel;
    }

    /**
     * @return string[]
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_NEWS => "Novinky",
            self::CATEGORY_CHRONICLE => "Kronika",
            self::CATEGORY_POETRY => "Poezie",
            self::CATEGORY_SHORT_STORY => "Povídky",
            self::CATEGORY_ESSAY => "Eseje",
            self::CATEGORY_NOVELLA => "Novely",
            self::CATEGORY_FAIRY_TALE => "Pohádky",
            self::CATEGORY_UNCATEGORIZED => "Nezařazené",
        ];
    }

    protected function getterCreatedAt(): string
    {
        return $this->localeModel->formatDateTime($this->created);
    }

    protected function getterUpdatedAt(): string
    {
        return $this->localeModel->formatDateTime($this->updated);
    }

    protected function getterCategoryCZ(): string
    {
        return self::getCategories()[$this->category];
    }

    protected function getterCommentsCount(): int
    {
        return $this->comments->toCollection()->findBy(["deleted" => false,])->countStored();
    }
}
