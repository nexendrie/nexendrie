<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * News
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
 */
class Article extends Entity {
  const CATEGORY_NEWS = "news";
  
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterAddedAt() {
    return $this->localeModel->formatDateTime($this->added);
  }
}
?>