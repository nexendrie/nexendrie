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
      "guestRole" => 13,
      "loggedInRole" => 12,
      "bannedRole" => 14
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
      "loanInterest" => 15,
      "buildMonastery" => 1000,
      "buildCastle" => 1500,
      "foundGuild" => 1000,
      "foundOrder" => 1200
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
    $builder->addDefinition($this->prefix("model.group"))
      ->setFactory("Nexendrie\Model\Group");
    $builder->addDefinition($this->prefix("model.market"))
      ->setFactory("Nexendrie\Model\Market");
    $builder->addDefinition($this->prefix("model.messenger"))
      ->setFactory("Nexendrie\Model\Messenger");
    $builder->addDefinition($this->prefix("model.polls"))
      ->setFactory("Nexendrie\Model\Polls");
    $builder->addDefinition($this->prefix("model.profile"))
      ->setFactory("Nexendrie\Model\Profile");
    $builder->addDefinition($this->prefix("model.rss"))
      ->setFactory("Nexendrie\Model\Rss");
    $builder->addDefinition($this->prefix("model.property"))
      ->setFactory("Nexendrie\Model\Property");
    $builder->addDefinition($this->prefix("model.job"))
      ->setFactory("Nexendrie\Model\Job");
    $builder->addDefinition($this->prefix("model.town"))
      ->setFactory("Nexendrie\Model\Town");
    $builder->addDefinition($this->prefix("model.mount"))
      ->setFactory("Nexendrie\Model\Mount");
    $builder->addDefinition($this->prefix("model.skills"))
      ->setFactory("Nexendrie\Model\Skills");
    $builder->addDefinition($this->prefix("model.chronicle"))
      ->setFactory("Nexendrie\Model\Chronicle");
    $builder->addDefinition($this->prefix("model.tavern"))
      ->setFactory("Nexendrie\Model\Tavern");
    $builder->addDefinition($this->prefix("model.inventory"))
      ->setFactory("Nexendrie\Model\Inventory");
    $builder->addDefinition($this->prefix("model.adventure"))
      ->setFactory("Nexendrie\Model\Adventure");
    $builder->addDefinition($this->prefix("model.combat"))
      ->setFactory("Nexendrie\Model\Combat");
    $builder->addDefinition($this->prefix("model.events"))
      ->setFactory("Nexendrie\Model\Events");
    $builder->addDefinition($this->prefix("model.house"))
      ->setFactory("Nexendrie\Model\House");
    $builder->addDefinition($this->prefix("model.itemSet"))
      ->setFactory("Nexendrie\Model\ItemSet");
    $builder->addDefinition($this->prefix("model.marriage"))
      ->setFactory("Nexendrie\Model\Marriage");
    $builder->addDefinition($this->prefix("model.elections"))
      ->setFactory("Nexendrie\Model\Elections");
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
    $builder->addDefinition($this->prefix("model.monastery"))
      ->setFactory("Nexendrie\Model\Monastery", array($config["fees"]["buildMonastery"]));
    $builder->addDefinition($this->prefix("model.castle"))
      ->setFactory("Nexendrie\Model\Castle", array($config["fees"]["buildCastle"]));
    $builder->addDefinition($this->prefix("model.guild"))
      ->setFactory("Nexendrie\Model\Guild", array($config["fees"]["foundGuild"]));
    $builder->addDefinition($this->prefix("model.order"))
      ->setFactory("Nexendrie\Model\Order", array($config["fees"]["foundOrder"]));
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
    $builder->addDefinition($this->prefix("component.poll"))
      ->setImplement("Nexendrie\Components\PollControlFactory");
    $builder->addDefinition($this->prefix("component.shop"))
      ->setImplement("Nexendrie\Components\ShopControlFactory");
    $builder->addDefinition($this->prefix("component.mountsMarket"))
      ->setImplement("Nexendrie\Components\MountsMarketControlFactory");
    $builder->addDefinition($this->prefix("component.academy"))
      ->setImplement("Nexendrie\Components\AcademyControlFactory");
    $builder->addDefinition($this->prefix("component.townsMarket"))
      ->setImplement("Nexendrie\Components\TownsMarketControlFactory");
    $builder->addDefinition($this->prefix("component.help"))
      ->setImplement("Nexendrie\Components\HelpControlFactory");
    $builder->addDefinition($this->prefix("component.stables"))
      ->setImplement("Nexendrie\Components\StablesControlFactory");
    $builder->addDefinition($this->prefix("component.prison"))
      ->setImplement("Nexendrie\Components\PrisonControlFactory");
    $builder->addDefinition($this->prefix("component.tavern"))
      ->setImplement("Nexendrie\Components\TavernControlFactory");
    $builder->addDefinition($this->prefix("component.adventure"))
      ->setImplement("Nexendrie\Components\AdventureControlFactory");
    $builder->addDefinition($this->prefix("component.history"))
      ->setImplement("Nexendrie\Components\HistoryControlFactory");
    $builder->addDefinition($this->prefix("component.wedding"))
      ->setImplement("Nexendrie\Components\WeddingControlFactory");
    $builder->addDefinition($this->prefix("component.elections"))
      ->setImplement("Nexendrie\Components\ElectionsControlFactory");
  }
  
  /**
   * @return void
   */
  protected function addForms() {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("form.addEditArticle"))
      ->setFactory("Nexendrie\Forms\AddEditArticleFormFactory");
    $builder->addDefinition($this->prefix("form.addEditPoll"))
      ->setFactory("Nexendrie\Forms\AddEditPollFormFactory");
    $builder->addDefinition($this->prefix("form.newMessage"))
      ->setFactory("Nexendrie\Forms\NewMessageFormFactory");
    $builder->addDefinition($this->prefix("form.register"))
      ->setFactory("Nexendrie\Forms\RegisterFormFactory");
    $builder->addDefinition($this->prefix("form.login"))
      ->setFactory("Nexendrie\Forms\LoginFormFactory");
    $builder->addDefinition($this->prefix("form.userSettings"))
      ->setFactory("Nexendrie\Forms\UserSettingsFormFactory");
    $builder->addDefinition($this->prefix("form.addComment"))
      ->setFactory("Nexendrie\Forms\AddCommentFormFactory");
    $builder->addDefinition($this->prefix("form.editGroup"))
      ->setFactory("Nexendrie\Forms\EditGroupFormFactory");
    $builder->addDefinition($this->prefix("form.systemSettings"))
      ->setFactory("Nexendrie\Forms\SystemSettingsFormFactory");
    $builder->addDefinition($this->prefix("form.editUser"))
      ->setFactory("Nexendrie\Forms\EditUserFormFactory");
    $builder->addDefinition($this->prefix("form.addEditShop"))
      ->setFactory("Nexendrie\Forms\AddEditShopFormFactory");
    $builder->addDefinition($this->prefix("form.addEditItem"))
      ->setFactory("Nexendrie\Forms\AddEditItemFormFactory");
    $builder->addDefinition($this->prefix("form.addEditJob"))
      ->setFactory("Nexendrie\Forms\AddEditJobFormFactory");
    $builder->addDefinition($this->prefix("form.addEditJobMessage"))
      ->setFactory("Nexendrie\Forms\AddEditJobMessageFormFactory");
    $builder->addDefinition($this->prefix("form.addEditTown"))
      ->setFactory("Nexendrie\Forms\AddEditTownFormFactory");
    $builder->addDefinition($this->prefix("form.addEditMount"))
      ->setFactory("Nexendrie\Forms\AddEditMountFormFactory");
    $builder->addDefinition($this->prefix("form.addEditSkill"))
      ->setFactory("Nexendrie\Forms\AddEditSkillFormFactory");
    $builder->addDefinition($this->prefix("form.manageMount"))
      ->setFactory("Nexendrie\Forms\ManageMountFormFactory");
    $builder->addDefinition($this->prefix("form.manageTown"))
      ->setFactory("Nexendrie\Forms\ManageTownFormFactory");
    $builder->addDefinition($this->prefix("form.banUser"))
      ->setFactory("Nexendrie\Forms\BanUserFormFactory");
    $builder->addDefinition($this->prefix("form.takeLoan"))
      ->setFactory("Nexendrie\Forms\TakeLoanFormFactory");
    $builder->addDefinition($this->prefix("form.addEditMeal"))
      ->setFactory("Nexendrie\Forms\AddEditMealFormFactory");
    $builder->addDefinition($this->prefix("form.addEditAdventure"))
      ->setFactory("Nexendrie\Forms\AddEditAdventureFormFactory");
    $builder->addDefinition($this->prefix("form.addEditAdventureEnemy"))
      ->setFactory("Nexendrie\Forms\AddEditAdventureEnemyFormFactory");
    $builder->addDefinition($this->prefix("form.buildMonastery"))
      ->setFactory("Nexendrie\Forms\BuildMonasteryFormFactory");
    $builder->addDefinition($this->prefix("form.monasteryDonate"))
      ->setFactory("Nexendrie\Forms\MonasteryDonateFormFactory");
    $builder->addDefinition($this->prefix("form.manageMonastery"))
      ->setFactory("Nexendrie\Forms\ManageMonasteryFormFactory");
    $builder->addDefinition($this->prefix("form.appointMayor"))
      ->setFactory("Nexendrie\Forms\AppointMayorFormFactory");
    $builder->addDefinition($this->prefix("form.buildCastle"))
      ->setFactory("Nexendrie\Forms\BuildCastleFormFactory");
    $builder->addDefinition($this->prefix("form.gift"))
      ->setFactory("Nexendrie\Forms\GiftFormFactory");
    $builder->addDefinition($this->prefix("form.foundTown"))
      ->setFactory("Nexendrie\Forms\FoundTownFormFactory");
    $builder->addDefinition($this->prefix("form.makeCitizen"))
      ->setFactory("Nexendrie\Forms\MakeCitizenFormFactory");
    $builder->addDefinition($this->prefix("form.addEditEvent"))
      ->setFactory("Nexendrie\Forms\AddEditEventFormFactory");
    $builder->addDefinition($this->prefix("form.foundGuild"))
      ->setFactory("Nexendrie\Forms\FoundGuildFormFactory");
    $builder->addDefinition($this->prefix("form.manageGuild"))
      ->setFactory("Nexendrie\Forms\ManageGuildFormFactory");
    $builder->addDefinition($this->prefix("form.foundOrder"))
      ->setFactory("Nexendrie\Forms\FoundOrderFormFactory");
    $builder->addDefinition($this->prefix("form.manageOrder"))
      ->setFactory("Nexendrie\Forms\ManageOrderFormFactory");
    $builder->addDefinition($this->prefix("form.addEditItemSet"))
      ->setFactory("Nexendrie\Forms\AddEditItemSetFormFactory");
    $builder->addDefinition($this->prefix("form.manageCastle"))
      ->setFactory("Nexendrie\Forms\ManageCastleFormFactory");
    $builder->addDefinition($this->prefix("form.changeWeddingTerm"))
      ->setFactory("Nexendrie\Forms\ChangeWeddingTermFormFactory");
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
$user->authenticatedRole = $groupModel->get(?)->singleName;
\Nella\Forms\DateTime\DateInput::register();
\Nella\Forms\DateTime\DateTimeInput::register();', array($roles["guestRole"], $roles["loggedInRole"]));
  }
}
?>
