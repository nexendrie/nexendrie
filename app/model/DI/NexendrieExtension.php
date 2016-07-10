<?php
namespace Nexendrie\Model\DI;

use Nexendrie;

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
      ->setFactory(Nexendrie\Model\Group::class);
    $builder->addDefinition($this->prefix("model.market"))
      ->setFactory(Nexendrie\Model\Market::class);
    $builder->addDefinition($this->prefix("model.messenger"))
      ->setFactory(Nexendrie\Model\Messenger::class);
    $builder->addDefinition($this->prefix("model.polls"))
      ->setFactory(Nexendrie\Model\Polls::class);
    $builder->addDefinition($this->prefix("model.profile"))
      ->setFactory(Nexendrie\Model\Profile::class);
    $builder->addDefinition($this->prefix("model.rss"))
      ->setFactory(Nexendrie\Model\Rss::class);
    $builder->addDefinition($this->prefix("model.property"))
      ->setFactory(Nexendrie\Model\Property::class);
    $builder->addDefinition($this->prefix("model.job"))
      ->setFactory(Nexendrie\Model\Job::class);
    $builder->addDefinition($this->prefix("model.town"))
      ->setFactory(Nexendrie\Model\Town::class);
    $builder->addDefinition($this->prefix("model.mount"))
      ->setFactory(Nexendrie\Model\Mount::class);
    $builder->addDefinition($this->prefix("model.skills"))
      ->setFactory(Nexendrie\Model\Skills::class);
    $builder->addDefinition($this->prefix("model.chronicle"))
      ->setFactory(Nexendrie\Model\Chronicle::class);
    $builder->addDefinition($this->prefix("model.tavern"))
      ->setFactory(Nexendrie\Model\Tavern::class);
    $builder->addDefinition($this->prefix("model.inventory"))
      ->setFactory(Nexendrie\Model\Inventory::class);
    $builder->addDefinition($this->prefix("model.adventure"))
      ->setFactory(Nexendrie\Model\Adventure::class);
    $builder->addDefinition($this->prefix("model.combat"))
      ->setFactory(Nexendrie\Model\Combat::class);
    $builder->addDefinition($this->prefix("model.events"))
      ->setFactory(Nexendrie\Model\Events::class);
    $builder->addDefinition($this->prefix("model.house"))
      ->setFactory(Nexendrie\Model\House::class);
    $builder->addDefinition($this->prefix("model.itemSet"))
      ->setFactory(Nexendrie\Model\ItemSet::class);
    $builder->addDefinition($this->prefix("model.marriage"))
      ->setFactory(Nexendrie\Model\Marriage::class);
    $builder->addDefinition($this->prefix("model.elections"))
      ->setFactory(Nexendrie\Model\Elections::class);
    $builder->addDefinition($this->prefix("model.article"))
      ->setFactory(Nexendrie\Model\Article::class, array($config["pagination"]["news"]));
    $builder->addDefinition($this->prefix("model.userManager"))
      ->setFactory(Nexendrie\Model\UserManager::class, array($config["roles"], $config["newUser"]));
    $builder->addDefinition($this->prefix("model.locale"))
      ->setFactory(Nexendrie\Model\Locale::class, array($config["locale"]));
    $builder->addDefinition($this->prefix("model.bank"))
       ->setFactory(Nexendrie\Model\Bank::class, array($config["fees"]["loanInterest"]));
    $builder->addDefinition($this->prefix("model.taxes"))
        ->setFactory(Nexendrie\Model\Taxes::class, array($config["fees"]["incomeTax"]));
    $builder->addDefinition($this->prefix("model.monastery"))
      ->setFactory(Nexendrie\Model\Monastery::class, array($config["fees"]["buildMonastery"]));
    $builder->addDefinition($this->prefix("model.castle"))
      ->setFactory(Nexendrie\Model\Castle::class, array($config["fees"]["buildCastle"]));
    $builder->addDefinition($this->prefix("model.guild"))
      ->setFactory(Nexendrie\Model\Guild::class, array($config["fees"]["foundGuild"]));
    $builder->addDefinition($this->prefix("model.order"))
      ->setFactory(Nexendrie\Model\Order::class, array($config["fees"]["foundOrder"]));
    $builder->addDefinition("cache.cache")
      ->setFactory(\Nette\Caching\Cache::class, array("@cache.storage", "data"));
    $builder->addDefinition($this->prefix("model.settingsRepository"))
      ->setFactory(Nexendrie\Model\SettingsRepository::class, array($config));
    $builder->addDefinition($this->prefix("model.authorizator"))
      ->setFactory("Nexendrie\Model\AuthorizatorFactory::create");
    $builder->removeDefinition("router");
    $builder->addDefinition("router")
      ->setFactory("Nexendrie\Model\RouterFactory::create");
    $builder->addDefinition($this->prefix("cronTasks"))
       ->setFactory(Nexendrie\CronTasks::class)
       ->addTag("cronner.tasks");
  }
  
  /**
   * @return void
   */
  protected function addComponents() {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("component.poll"))
      ->setImplement(Nexendrie\Components\PollControlFactory::class);
    $builder->addDefinition($this->prefix("component.shop"))
      ->setImplement(Nexendrie\Components\ShopControlFactory::class);
    $builder->addDefinition($this->prefix("component.mountsMarket"))
      ->setImplement(Nexendrie\Components\MountsMarketControlFactory::class);
    $builder->addDefinition($this->prefix("component.academy"))
      ->setImplement(Nexendrie\Components\AcademyControlFactory::class);
    $builder->addDefinition($this->prefix("component.townsMarket"))
      ->setImplement(Nexendrie\Components\TownsMarketControlFactory::class);
    $builder->addDefinition($this->prefix("component.help"))
      ->setImplement(Nexendrie\Components\HelpControlFactory::class);
    $builder->addDefinition($this->prefix("component.stables"))
      ->setImplement(Nexendrie\Components\StablesControlFactory::class);
    $builder->addDefinition($this->prefix("component.prison"))
      ->setImplement(Nexendrie\Components\PrisonControlFactory::class);
    $builder->addDefinition($this->prefix("component.tavern"))
      ->setImplement(Nexendrie\Components\TavernControlFactory::class);
    $builder->addDefinition($this->prefix("component.adventure"))
      ->setImplement(Nexendrie\Components\AdventureControlFactory::class);
    $builder->addDefinition($this->prefix("component.history"))
      ->setImplement(Nexendrie\Components\HistoryControlFactory::class);
    $builder->addDefinition($this->prefix("component.wedding"))
      ->setImplement(Nexendrie\Components\WeddingControlFactory::class);
    $builder->addDefinition($this->prefix("component.elections"))
      ->setImplement(Nexendrie\Components\ElectionsControlFactory::class);
  }
  
  /**
   * @return void
   */
  protected function addForms() {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("form.addEditArticle"))
      ->setFactory(Nexendrie\Forms\AddEditArticleFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditPoll"))
      ->setFactory(Nexendrie\Forms\AddEditPollFormFactory::class);
    $builder->addDefinition($this->prefix("form.newMessage"))
      ->setFactory(Nexendrie\Forms\NewMessageFormFactory::class);
    $builder->addDefinition($this->prefix("form.register"))
      ->setFactory(Nexendrie\Forms\RegisterFormFactory::class);
    $builder->addDefinition($this->prefix("form.login"))
      ->setFactory(Nexendrie\Forms\LoginFormFactory::class);
    $builder->addDefinition($this->prefix("form.userSettings"))
      ->setFactory(Nexendrie\Forms\UserSettingsFormFactory::class);
    $builder->addDefinition($this->prefix("form.addComment"))
      ->setFactory(Nexendrie\Forms\AddCommentFormFactory::class);
    $builder->addDefinition($this->prefix("form.editGroup"))
      ->setFactory(Nexendrie\Forms\EditGroupFormFactory::class);
    $builder->addDefinition($this->prefix("form.systemSettings"))
      ->setFactory(Nexendrie\Forms\SystemSettingsFormFactory::class);
    $builder->addDefinition($this->prefix("form.editUser"))
      ->setFactory(Nexendrie\Forms\EditUserFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditShop"))
      ->setFactory(Nexendrie\Forms\AddEditShopFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditItem"))
      ->setFactory(Nexendrie\Forms\AddEditItemFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditJob"))
      ->setFactory(Nexendrie\Forms\AddEditJobFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditJobMessage"))
      ->setFactory(Nexendrie\Forms\AddEditJobMessageFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditTown"))
      ->setFactory(Nexendrie\Forms\AddEditTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditMount"))
      ->setFactory(Nexendrie\Forms\AddEditMountFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditSkill"))
      ->setFactory(Nexendrie\Forms\AddEditSkillFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageMount"))
      ->setFactory(Nexendrie\Forms\ManageMountFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageTown"))
      ->setFactory(Nexendrie\Forms\ManageTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.banUser"))
      ->setFactory(Nexendrie\Forms\BanUserFormFactory::class);
    $builder->addDefinition($this->prefix("form.takeLoan"))
      ->setFactory(Nexendrie\Forms\TakeLoanFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditMeal"))
      ->setFactory(Nexendrie\Forms\AddEditMealFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditAdventure"))
      ->setFactory(Nexendrie\Forms\AddEditAdventureFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditAdventureEnemy"))
      ->setFactory(Nexendrie\Forms\AddEditAdventureEnemyFormFactory::class);
    $builder->addDefinition($this->prefix("form.buildMonastery"))
      ->setFactory(Nexendrie\Forms\BuildMonasteryFormFactory::class);
    $builder->addDefinition($this->prefix("form.monasteryDonate"))
      ->setFactory(Nexendrie\Forms\MonasteryDonateFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageMonastery"))
      ->setFactory(Nexendrie\Forms\ManageMonasteryFormFactory::class);
    $builder->addDefinition($this->prefix("form.appointMayor"))
      ->setFactory(Nexendrie\Forms\AppointMayorFormFactory::class);
    $builder->addDefinition($this->prefix("form.buildCastle"))
      ->setFactory(Nexendrie\Forms\BuildCastleFormFactory::class);
    $builder->addDefinition($this->prefix("form.gift"))
      ->setFactory(Nexendrie\Forms\GiftFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundTown"))
      ->setFactory(Nexendrie\Forms\FoundTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.makeCitizen"))
      ->setFactory(Nexendrie\Forms\MakeCitizenFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditEvent"))
      ->setFactory(Nexendrie\Forms\AddEditEventFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundGuild"))
      ->setFactory(Nexendrie\Forms\FoundGuildFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageGuild"))
      ->setFactory(Nexendrie\Forms\ManageGuildFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundOrder"))
      ->setFactory(Nexendrie\Forms\FoundOrderFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageOrder"))
      ->setFactory(Nexendrie\Forms\ManageOrderFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditItemSet"))
      ->setFactory(Nexendrie\Forms\AddEditItemSetFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageCastle"))
      ->setFactory(Nexendrie\Forms\ManageCastleFormFactory::class);
    $builder->addDefinition($this->prefix("form.changeWeddingTerm"))
      ->setFactory(Nexendrie\Forms\ChangeWeddingTermFormFactory::class);
  }
  
  /**
   * @return void
   */
  function afterCompile(\Nette\PhpGenerator\ClassType $class) {
    $roles = $this->getConfig($this->defaults)["roles"];
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$groupModel = $this->getByType(?);
$user = $this->getByType("Nette\Security\User");
$user->guestRole = $groupModel->get(?)->singleName;
$user->authenticatedRole = $groupModel->get(?)->singleName;
\Nella\Forms\DateTime\DateInput::register();
\Nella\Forms\DateTime\DateTimeInput::register();', array(Nexendrie\Model\Group::class, $roles["guestRole"], $roles["loggedInRole"]));
  }
}
?>
