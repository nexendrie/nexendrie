<?php
namespace Nexendrie\Model\DI;

/**
 * Nexendrie Extension for DIC
 *
 * @author Jakub Konečný
 */
class NexendrieExtension extends \Nette\DI\CompilerExtension {
  /** @var array */
  protected $defaults = array(
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
    ),
    "pagination" => array(
      "news" => 10
    )
  );
  
  /**
   * @return void
   */
  function loadConfiguration() {
    $this->addModels();
    $this->addComponents();
    $this->addForms();
  }
  
  /**
   * @return void
   */
  protected function addModels() {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    $services = array(
      "group", "market", "messenger", "polls", "profile", "rss"
    );
    foreach($services as $service) {
      $builder->addDefinition($this->prefix("model.$service"))
        ->setFactory("Nexendrie\Model\\" . ucfirst($service));
    }
    $builder->addDefinition($this->prefix("model.news"))
      ->setFactory("Nexendrie\Model\News", array($config["pagination"]["news"]));
    $builder->addDefinition($this->prefix("model.userManager"))
      ->setFactory("Nexendrie\Model\UserManager", array($config["roles"]));
    $builder->addDefinition($this->prefix("model.locale"))
      ->setFactory("Nexendrie\Model\Locale", array($config["locale"]));
    $builder->addDefinition("cache.cache")
      ->setFactory("Nette\Caching\Cache", array("@cache.storage", "data"));
    $builder->addDefinition($this->prefix("model.settingsRepository"))
      ->setFactory("Nexendrie\Model\SettingsRepository", array($config));
    $builder->addDefinition($this->prefix("model.authorizator"))
      ->setFactory("Nexendrie\Model\AuthorizatorFactory::create");
    $builder->removeDefinition("router");
    $builder->addDefinition("router")
      ->setFactory("Nexendrie\Model\RouterFactory::create");
  }
  
  /**
   * @return void
   */
  protected function addComponents() {
    $builder = $this->getContainerBuilder();
    $components = array("poll", "shop");
    foreach($components as $component) {
      $builder->addDefinition($this->prefix("component.$component"))
        ->setImplement("Nexendrie\Components\\". ucfirst($component) . "ControlFactory");
    }
  }
  
  /**
   * @return void
   */
  protected function addForms() {
    $builder = $this->getContainerBuilder();
    $forms = array(
      "addEditNews", "addEditPoll", "newMessage", "register", "login",
      "userSettings", "addComment", "editGroup", "systemSettings", "editUser"
    );
    foreach($forms as $form) {
      $builder->addDefinition($this->prefix("form.$form"))
        ->setFactory("Nexendrie\Forms\\" . ucfirst($form) . "FormFactory");
    }
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