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
 * @property string $category {enum self::CATEGORY_*}
 * @property int $added
 * @property-read string $addedAt {virtual}
 * @property bool $allowedComments {default true}
 * @property OneHasMany|Comment[] $comments {1:m Comment::$article}
 * @property-read string $categoryCZ {virtual}
 */
class Article extends \Nextras\Orm\Entity\Entity {
  public const CATEGORY_NEWS = "news";
  public const CATEGORY_CHRONICLE = "chronicle";
  public const CATEGORY_POETRY = "poetry";
  public const CATEGORY_SHORT_STORY = "short_story";
  public const CATEGORY_ESSAY = "essay";
  public const CATEGORY_NOVELLA = "novella";
  public const CATEGORY_FAIRY_TALE = "fairy_tale";
  public const CATEGORY_UNCATEGORIZED = "uncategorized";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  /**
   * @return string[]
   */
  public static function getCategories(): array {
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
  
  protected function getterAddedAt(): string {
    return $this->localeModel->formatDateTime($this->added);
  }
  
  protected function getterCategoryCZ(): string {
    return self::getCategories()[$this->category];
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->added = time();
  }
}
?>