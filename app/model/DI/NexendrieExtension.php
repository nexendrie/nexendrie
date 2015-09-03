<?php
namespace Nexendrie\Model\DI;

/**
 * Nexendrie Extension for DIC
 *
 * @author Jakub Konečný
 */
class NexendrieExtension extends \Nette\DI\CompilerExtension {
  /** @var array */
  public $defaults = array(
    "roles" => array(
      "guestRole" => 9,
      "loggedInRole" => 8,
      "bannedRole" => 10
    ),
    "locale" => array(
      "dateFormat" => "j.n.Y",
      "dateTimeFormat" => "j.n.Y G:i",
      "plural" => array(
        0 => 1, "2-4", 5
      )
    )
  );
  
  /**
   * @return void
   */
  function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    $services = array(
      "Group", "Market", "Messenger", "News", "Polls", "Profile", "Rss"
    );
    foreach($services as $service) {
      $builder->addDefinition($this->prefix(lcfirst($service)))
        ->setFactory("Nexendrie\Model\\" . $service);
    }
    $builder->addDefinition("nexendrie.userManager")
      ->setFactory("Nexendrie\Model\UserManager", array($config["roles"]));
    $builder->addDefinition("nexendrie.locale")
      ->setFactory("Nexendrie\Model\Locale", array($config["locale"]));
    $builder->addDefinition("cache.cache")
      ->setFactory("Nette\Caching\Cache", array("@cache.storage", "data"));
    $builder->addDefinition($this->prefix("authorizator"))
      ->setFactory("Nexendrie\Model\Authorizator::create");
    $builder->removeDefinition("router");
    $builder->addDefinition("router")
      ->setFactory("Nexendrie\Model\RouterFactory::create");
  }
  
  function afterCompile(\Nette\PhpGenerator\ClassType $class) {
    $roles = $this->getConfig($this->defaults)["roles"];
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$groupModel = $this->getByType("Nexendrie\Model\Group");
$user = $this->getByType("Nette\Security\User");
$user->guestRole = $groupModel->get(?)->singleName;
$user->authenticatedRole = $groupModel->get(?)->singleName;', array($roles["guestRole"], $roles["loggedInRole"]));
  }
}
?>