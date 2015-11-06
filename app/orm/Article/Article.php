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
  
  /** @var \Nexendrie\Model\Locale $localeModel */
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