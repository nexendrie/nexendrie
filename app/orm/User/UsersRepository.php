<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository;

/**
 * @author Jakub Konečný
 * @method User|NULL getById($id)
 * @method User|NULL getByUsername(string $username)
 * @method User|NULL getByPublicname(string $publicname)
 * @method User|NULL getByEmail(string $email)
 */
class UsersRepository extends Repository {

}
?>