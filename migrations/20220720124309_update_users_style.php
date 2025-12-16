<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateUsersStyle extends AbstractMigration
{
    public function change(): void
    {
        if (!$this->isMigratingUp()) {
            return;
        }
        $this->execute("UPDATE users SET style='nexendrie' WHERE style IN ('dark-sky', 'blue-sky')");
    }
}
