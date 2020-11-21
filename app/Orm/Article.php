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
 * @property bool $allowedComments {default true}
 * @property OneHasMany|Comment[] $comments {1:m Comment::$article}
 * @property-read string $categoryCZ {virtual}
 * @property-read int $commentsCount {virtual}
 */
final class Article extends BaseEntity {
  public const CATEGORY_NEWS = "news";
  public const CATEGORY_CHRONICLE = "chronicle";
  public const CATEGORY_POETRY = "poetry";
  public const CATEGORY_SHORT_STORY = "short_story";
  public const CATEGORY_ESSAY = "essay";
  public const CATEGORY_NOVELLA = "novella";
  public const CATEGORY_FAIRY_TALE = "fairy_tale";
  public const CATEGORY_UNCATEGORIZED = "uncategorized";

  protected \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  /**
   * @return string[]
   */
  public static function getCategories(): array {
    return [
      static::CATEGORY_NEWS => "Novinky",
      static::CATEGORY_CHRONICLE => "Kronika",
      static::CATEGORY_POETRY => "Poezie",
      static::CATEGORY_SHORT_STORY => "Povídky",
      static::CATEGORY_ESSAY => "Eseje",
      static::CATEGORY_NOVELLA => "Novely",
      static::CATEGORY_FAIRY_TALE => "Pohádky",
      static::CATEGORY_UNCATEGORIZED => "Nezařazené",
    ];
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
  
  protected function getterCategoryCZ(): string {
    return static::getCategories()[$this->category];
  }

  protected function getterCommentsCount(): int {
    return $this->comments->toCollection()->findBy(["deleted" => false, ])->countStored();
  }
}
?>