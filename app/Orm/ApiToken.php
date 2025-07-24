<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Api token
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $token
 * @property User $user {m:1 User::$apiTokens}
 * @property int $expire
 * @property-read string $expireAt {virtual}
 * @property int $created
 * @property-read string $createdAt {virtual}
 */
final class ApiToken extends BaseEntity {
  private \Nexendrie\Model\Locale $localeModel;

  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }

  protected function getterExpireAt(): string {
    return $this->localeModel->formatDateTime($this->expire);
  }

  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
}
?>