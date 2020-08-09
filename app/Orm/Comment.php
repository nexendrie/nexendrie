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
 * @property int $created
 * @property-read string $createdAt {virtual}
 * @property bool $deleted
 */
final class Comment extends BaseEntity {
  protected \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
}
?>