<?php
namespace Nexendrie\Model\DI;

/**
 * Nexendrie Extension for DIC
 *
 * @author Jakub Konečný
 */
class NexendrieExtension extends \Nette\DI\CompilerExtension {
  /**
   * @return void
   */
  function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $services = array(
      "Group", "Market", "Messenger", "News", "Polls", "Profile", "Rss",
      "UserManager"
    );
    foreach($services as $service) {
      $builder->addDefinition($this->prefix(lcfirst($service)))
        ->setFactory("Nexendrie\Model\\" . $service);
    }
    $builder->addDefinition("cache.cache")
      ->setFactory("Nette\Caching\Cache", array("@cache.storage", "data"));
    $builder->addDefinition($this->prefix("authorizator"))
      ->setFactory("Nexendrie\Model\Authorizator::create");
    $builder->removeDefinition("router");
    $builder->addDefinition("router")
      ->setFactory("Nexendrie\Model\RouterFactory::create");
  }
  
  function afterCompile(\Nette\PhpGenerator\ClassType $class) {
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$groupModel = $this->getByType("Nexendrie\Model\Group");
$user = $this->getByType("Nette\Security\User");
$user->guestRole = $groupModel->get(GUEST_ROLE)->single_name;
$user->authenticatedRole = $groupModel->get(LOGGEDIN_ROLE)->single_name;');
  }
}
?>