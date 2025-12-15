<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Invitation
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $email
 * @property User $inviter {1:1 User, isMain=true, oneSided=true}
 * @property int $created
 * @property-read string $createdAt {virtual}
 */
final class Invitation extends BaseEntity {
  private \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
}
?>