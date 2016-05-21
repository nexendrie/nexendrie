<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Article
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $text
 * @property User $author {m:1 User}
 * @property string $category {enum self::CATEGORY_*}
 * @property int $added
 * @property-read string $addedAt {virtual}
 * @property bool $allowedComments {default 1}
 * @property OneHasMany|Comment[] $comments {1:m Comment::$article}
 * @property-read string $categoryCZ {virtual}
 */
class Article extends \Nextras\Orm\Entity\Entity {
  const CATEGORY_NEWS = "news";
  const CATEGORY_CHRONICLE = "chronicle";
  const CATEGORY_POETRY = "poetry";
  const CATEGORY_SHORT_STORY = "short_story";
  const CATEGORY_ESSAY = "essay";
  const CATEGORY_NOVELLA = "novella";
  const CATEGORY_FAIRY_TALE = "fairy_tale";
  const CATEGORY_UNCATEGORIZED = "uncategorized";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  /**
   * @return string[]
   */
  static function getCategories() {
    return array(
      self::CATEGORY_NEWS => "Novinky",
      self::CATEGORY_CHRONICLE => "Kronika",
      self::CATEGORY_POETRY => "Poezie",
      self::CATEGORY_SHORT_STORY => "Povídky",
      self::CATEGORY_ESSAY => "Eseje",
      self::CATEGORY_NOVELLA => "Novely",
      self::CATEGORY_FAIRY_TALE => "Pohádky",
      self::CATEGORY_UNCATEGORIZED => "Nezařazené",
    );
  }
  
  protected function getterAddedAt() {
    return $this->localeModel->formatDateTime($this->added);
  }
  
  protected function getterCategoryCZ() {
    return self::getCategories()[$this->category];
  }
}
?>