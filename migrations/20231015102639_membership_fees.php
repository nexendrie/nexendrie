<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MembershipFees extends AbstractMigration
{
    public function up(): void
    {
        $this->execute("UPDATE guild_ranks SET guild_fee=135 WHERE id=1");
        $this->execute("UPDATE guild_ranks SET guild_fee=110 WHERE id=2");
        $this->execute("UPDATE guild_ranks SET guild_fee=75 WHERE id=3");
        $this->execute("UPDATE guild_ranks SET guild_fee=50 WHERE id=4");

        $this->execute("UPDATE order_ranks SET order_fee=150 WHERE id=1");
        $this->execute("UPDATE order_ranks SET order_fee=125 WHERE id=2");
        $this->execute("UPDATE order_ranks SET order_fee=90 WHERE id=3");
        $this->execute("UPDATE order_ranks SET order_fee=65 WHERE id=4");
    }

    public function down(): void
    {
        $this->execute("UPDATE guild_ranks SET guild_fee=135 WHERE id=4");
        $this->execute("UPDATE guild_ranks SET guild_fee=110 WHERE id=3");
        $this->execute("UPDATE guild_ranks SET guild_fee=75 WHERE id=2");
        $this->execute("UPDATE guild_ranks SET guild_fee=50 WHERE id=1");

        $this->execute("UPDATE order_ranks SET order_fee=150 WHERE id=4");
        $this->execute("UPDATE order_ranks SET order_fee=125 WHERE id=3");
        $this->execute("UPDATE order_ranks SET order_fee=90 WHERE id=2");
        $this->execute("UPDATE order_ranks SET order_fee=65 WHERE id=1");
    }
}
