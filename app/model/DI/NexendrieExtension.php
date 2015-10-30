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
      "guestRole" => 12,
      "loggedInRole" => 11,
      "bannedRole" => 13
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
    ),
    "newUser" => array(
      "style" => "blue-sky",
      "money" => 30,
      "town" => 3
    ),
    "fees" => array(
      "incomeTax" => 10,
      "loanInterest" => 15
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
      "group", "market", "messenger", "polls", "profile", "rss", "property", "job",
      "town", "mount", "skills", "chronicle", "tavern", "equipment"
    );
    foreach($services as $service) {
      $builder->addDefinition($this->prefix("model.$service"))
        ->setFactory("Nexendrie\Model\\" . ucfirst($service));
    }
    $builder->addDefinition($this->prefix("model.article"))
      ->setFactory("Nexendrie\Model\Article", array($config["pagination"]["news"]));
    $builder->addDefinition($this->prefix("model.userManager"))
      ->setFactory("Nexendrie\Model\UserManager", array($config["roles"], $config["newUser"]));
    $builder->addDefinition($this->prefix("model.locale"))
      ->setFactory("Nexendrie\Model\Locale", array($config["locale"]));
    $builder->addDefinition($this->prefix("model.bank"))
       ->setFactory("Nexendrie\Model\Bank", array($config["fees"]["loanInterest"]));
    $builder->addDefinition($this->prefix("model.taxes"))
        ->setFactory("Nexendrie\Model\Taxes", array($config["fees"]["incomeTax"]));
    $builder->addDefinition("cache.cache")
      ->setFactory("Nette\Caching\Cache", array("@cache.storage", "data"));
    $builder->addDefinition($this->prefix("model.settingsRepository"))
      ->setFactory("Nexendrie\Model\SettingsRepository", array($config));
    $builder->addDefinition($this->prefix("model.authorizator"))
      ->setFactory("Nexendrie\Model\AuthorizatorFactory::create");
    $builder->removeDefinition("router");
    $builder->addDefinition("router")
      ->setFactory("Nexendrie\Model\RouterFactory::create");
    $builder->addDefinition($this->prefix("cronTasks"))
       ->setFactory("Nexendrie\CronTasks")
       ->addTag("cronner.tasks");
  }
  
  /**
   * @return void
   */
  protected function addComponents() {
    $builder = $this->getContainerBuilder();
    $components = array(
      "poll", "shop", "mountsMarket", "academy", "townsMarket", "help", "stables",
      "prison", "tavern"
    );
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
      "addEditArticle", "addEditPoll", "newMessage", "register", "login",
      "userSettings", "addComment", "editGroup", "systemSettings", "editUser",
      "addEditShop", "addEditItem", "addEditJob", "addEditJobMessage", "addEditTown",
      "addEditMount", "addEditSkill", "manageMount", "manageTown", "banUser", "takeLoan",
      "addEditMeal"
    );
    foreach($forms as $form) {
      $builder->addDefinition($this->prefix("form.$form"))
        ->setFactory("Nexendrie\Forms\\" . ucfirst($form) . "FormFactory");
    }
  }
  
  /**
   * @return void
   */
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