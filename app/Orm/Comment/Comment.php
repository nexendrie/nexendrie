<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Comment
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $title
 * @property string $text
 * @property Article $article {m:1 Article::$comments}
 * @property User $author {m:1 User::$comments}
 * @property int $added
 * @property-read string $addedAt {virtual}
 */
class Comment extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterAddedAt(): string {
    return $this->localeModel->formatDateTime($this->added);
  }
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->added = time();
  }
}
?>