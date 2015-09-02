<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;


/**
 * Comment
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $text
 * @property News $news {m:1 News::$comments}
 * @property User $author {m:1 User::$comments}
 * @property int $added
 * @property-read string $addedAt {virtual}
 */
class Comment extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterAddedAt() {
    return $this->localeModel->formatDateTime($this->added);
  }
}
?>