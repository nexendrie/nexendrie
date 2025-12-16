<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Notification
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $title
 * @property string $body
 * @property string|null $icon
 * @property string $tag
 * @property string|null $targetUrl
 * @property User $user {m:1 User::$notificationQueue}
 * @property int $created
 */
final class Notification extends BaseEntity
{
}
