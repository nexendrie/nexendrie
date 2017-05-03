<?php
declare(strict_types=1);

namespace Nexendrie\Model\DI;

use Nexendrie;

/**
 * Nexendrie Extension for DIC
 *
 * @author Jakub Konečný
 */
class NexendrieExtension extends \Nette\DI\CompilerExtension {
  /** @var array */
  protected $defaults = [
    "roles" => [
      "guestRole" => 13,
      "loggedInRole" => 12,
      "bannedRole" => 14
    ],
    "locale" => [
      "dateFormat" => "j.n.Y",
      "dateTimeFormat" => "j.n.Y G:i",
      "plural" => [
        0 => 1, "2-4", 5
      ]
    ],
    "pagination" => [
      "news" => 10
    ],
    "newUser" => [
      "style" => "blue-sky",
      "money" => 30,
      "town" => 3
    ],
    "fees" => [
      "incomeTax" => 10,
      "loanInterest" => 15,
      "buildMonastery" => 1000,
      "buildCastle" => 1500,
      "foundGuild" => 1000,
      "foundOrder" => 1200
    ],
    "registration" => [
      "token" => ""
    ],
    "site" => [
      "versionSuffix" => ""
    ]
  ];
  
  /**
   * @return void
   */
  function loadConfiguration(): void {
    $this->addModels();
    $this->addComponents();
    $this->addForms();
  }
  
  /**
   * @return void
   */
  protected function addModels(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    $builder->addDefinition($this->prefix("model.group"))
      ->setClass(Nexendrie\Model\Group::class);
    $builder->addDefinition($this->prefix("model.market"))
      ->setClass(Nexendrie\Model\Market::class);
    $builder->addDefinition($this->prefix("model.messenger"))
      ->setClass(Nexendrie\Model\Messenger::class);
    $builder->addDefinition($this->prefix("model.polls"))
      ->setClass(Nexendrie\Model\Polls::class);
    $builder->addDefinition($this->prefix("model.profile"))
      ->setClass(Nexendrie\Model\Profile::class);
    $builder->addDefinition($this->prefix("model.rss"))
      ->setClass(Nexendrie\Model\Rss::class);
    $builder->addDefinition($this->prefix("model.property"))
      ->setClass(Nexendrie\Model\Property::class);
    $builder->addDefinition($this->prefix("model.job"))
      ->setClass(Nexendrie\Model\Job::class);
    $builder->addDefinition($this->prefix("model.town"))
      ->setClass(Nexendrie\Model\Town::class);
    $builder->addDefinition($this->prefix("model.mount"))
      ->setClass(Nexendrie\Model\Mount::class);
    $builder->addDefinition($this->prefix("model.skills"))
      ->setClass(Nexendrie\Model\Skills::class);
    $builder->addDefinition($this->prefix("model.chronicle"))
      ->setClass(Nexendrie\Model\Chronicle::class);
    $builder->addDefinition($this->prefix("model.tavern"))
      ->setClass(Nexendrie\Model\Tavern::class);
    $builder->addDefinition($this->prefix("model.inventory"))
      ->setClass(Nexendrie\Model\Inventory::class);
    $builder->addDefinition($this->prefix("model.adventure"))
      ->setClass(Nexendrie\Model\Adventure::class);
    $builder->addDefinition($this->prefix("model.combat"))
      ->setClass(Nexendrie\Model\Combat::class);
    $builder->addDefinition($this->prefix("model.events"))
      ->setClass(Nexendrie\Model\Events::class);
    $builder->addDefinition($this->prefix("model.house"))
      ->setClass(Nexendrie\Model\House::class);
    $builder->addDefinition($this->prefix("model.itemSet"))
      ->setClass(Nexendrie\Model\ItemSet::class);
    $builder->addDefinition($this->prefix("model.marriage"))
      ->setClass(Nexendrie\Model\Marriage::class);
    $builder->addDefinition($this->prefix("model.elections"))
      ->setClass(Nexendrie\Model\Elections::class);
    $builder->addDefinition($this->prefix("model.article"))
      ->setClass(Nexendrie\Model\Article::class);
    $builder->addDefinition($this->prefix("model.userManager"))
      ->setClass(Nexendrie\Model\UserManager::class);
    $builder->addDefinition($this->prefix("model.locale"))
      ->setClass(Nexendrie\Model\Locale::class);
    $builder->addDefinition($this->prefix("model.bank"))
       ->setClass(Nexendrie\Model\Bank::class);
    $builder->addDefinition($this->prefix("model.taxes"))
        ->setClass(Nexendrie\Model\Taxes::class);
    $builder->addDefinition($this->prefix("model.monastery"))
      ->setClass(Nexendrie\Model\Monastery::class);
    $builder->addDefinition($this->prefix("model.castle"))
      ->setClass(Nexendrie\Model\Castle::class);
    $builder->addDefinition($this->prefix("model.guild"))
      ->setClass(Nexendrie\Model\Guild::class);
    $builder->addDefinition($this->prefix("model.order"))
      ->setClass(Nexendrie\Model\Order::class);
    $builder->addDefinition("cache.cache")
      ->setFactory(\Nette\Caching\Cache::class, ["@cache.storage", "data"]);
    $appDir = $builder->expand("%appDir%");
    $builder->addDefinition($this->prefix("model.settingsRepository"))
      ->setFactory(Nexendrie\Model\SettingsRepository::class, [$config, $appDir]);
    $builder->addDefinition($this->prefix("model.authorizatorFactory"))
      ->setClass(Nexendrie\Model\AuthorizatorFactory::class);
    $builder->addDefinition($this->prefix("model.authorizator"))
      ->setClass(\Nette\Security\Permission::class)
      ->setFactory("@" . Nexendrie\Model\AuthorizatorFactory::class . "::create");
    $builder->removeDefinition("router");
    $builder->addDefinition($this->prefix("model.routerFactory"))
      ->setClass(Nexendrie\Model\RouterFactory::class);
    $builder->addDefinition($this->prefix("model.router"))
      ->setClass(\Nette\Application\Routers\RouteList::class)
      ->setFactory("@" . Nexendrie\Model\RouterFactory::class . "::create");
    $builder->addDefinition($this->prefix("cronTasks"))
       ->setClass(Nexendrie\Model\CronTasks::class)
       ->addTag("cronner.tasks");
  }
  
  /**
   * @return void
   */
  protected function addComponents(): void {
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
  protected function addForms(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("form.addEditArticle"))
      ->setClass(Nexendrie\Forms\AddEditArticleFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditPoll"))
      ->setClass(Nexendrie\Forms\AddEditPollFormFactory::class);
    $builder->addDefinition($this->prefix("form.newMessage"))
      ->setClass(Nexendrie\Forms\NewMessageFormFactory::class);
    $builder->addDefinition($this->prefix("form.register"))
      ->setClass(Nexendrie\Forms\RegisterFormFactory::class);
    $builder->addDefinition($this->prefix("form.login"))
      ->setClass(Nexendrie\Forms\LoginFormFactory::class);
    $builder->addDefinition($this->prefix("form.userSettings"))
      ->setClass(Nexendrie\Forms\UserSettingsFormFactory::class);
    $builder->addDefinition($this->prefix("form.addComment"))
      ->setClass(Nexendrie\Forms\AddCommentFormFactory::class);
    $builder->addDefinition($this->prefix("form.editGroup"))
      ->setClass(Nexendrie\Forms\EditGroupFormFactory::class);
    $builder->addDefinition($this->prefix("form.systemSettings"))
      ->setClass(Nexendrie\Forms\SystemSettingsFormFactory::class);
    $builder->addDefinition($this->prefix("form.editUser"))
      ->setClass(Nexendrie\Forms\EditUserFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditShop"))
      ->setClass(Nexendrie\Forms\AddEditShopFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditItem"))
      ->setClass(Nexendrie\Forms\AddEditItemFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditJob"))
      ->setClass(Nexendrie\Forms\AddEditJobFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditJobMessage"))
      ->setClass(Nexendrie\Forms\AddEditJobMessageFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditTown"))
      ->setClass(Nexendrie\Forms\AddEditTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditMount"))
      ->setClass(Nexendrie\Forms\AddEditMountFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditSkill"))
      ->setClass(Nexendrie\Forms\AddEditSkillFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageMount"))
      ->setClass(Nexendrie\Forms\ManageMountFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageTown"))
      ->setClass(Nexendrie\Forms\ManageTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.banUser"))
      ->setClass(Nexendrie\Forms\BanUserFormFactory::class);
    $builder->addDefinition($this->prefix("form.takeLoan"))
      ->setClass(Nexendrie\Forms\TakeLoanFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditMeal"))
      ->setClass(Nexendrie\Forms\AddEditMealFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditAdventure"))
      ->setClass(Nexendrie\Forms\AddEditAdventureFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditAdventureEnemy"))
      ->setClass(Nexendrie\Forms\AddEditAdventureEnemyFormFactory::class);
    $builder->addDefinition($this->prefix("form.buildMonastery"))
      ->setClass(Nexendrie\Forms\BuildMonasteryFormFactory::class);
    $builder->addDefinition($this->prefix("form.monasteryDonate"))
      ->setClass(Nexendrie\Forms\MonasteryDonateFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageMonastery"))
      ->setClass(Nexendrie\Forms\ManageMonasteryFormFactory::class);
    $builder->addDefinition($this->prefix("form.appointMayor"))
      ->setClass(Nexendrie\Forms\AppointMayorFormFactory::class);
    $builder->addDefinition($this->prefix("form.buildCastle"))
      ->setClass(Nexendrie\Forms\BuildCastleFormFactory::class);
    $builder->addDefinition($this->prefix("form.gift"))
      ->setClass(Nexendrie\Forms\GiftFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundTown"))
      ->setClass(Nexendrie\Forms\FoundTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.makeCitizen"))
      ->setClass(Nexendrie\Forms\MakeCitizenFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditEvent"))
      ->setClass(Nexendrie\Forms\AddEditEventFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundGuild"))
      ->setClass(Nexendrie\Forms\FoundGuildFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageGuild"))
      ->setClass(Nexendrie\Forms\ManageGuildFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundOrder"))
      ->setClass(Nexendrie\Forms\FoundOrderFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageOrder"))
      ->setClass(Nexendrie\Forms\ManageOrderFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditItemSet"))
      ->setClass(Nexendrie\Forms\AddEditItemSetFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageCastle"))
      ->setClass(Nexendrie\Forms\ManageCastleFormFactory::class);
    $builder->addDefinition($this->prefix("form.changeWeddingTerm"))
      ->setClass(Nexendrie\Forms\ChangeWeddingTermFormFactory::class);
  }
  
  /**
   * @param \Nette\PhpGenerator\ClassType $class
   * @return void
   */
  function afterCompile(\Nette\PhpGenerator\ClassType $class): void {
    $roles = $this->getConfig($this->defaults)["roles"];
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$groupModel = $this->getByType(?);
$user = $this->getByType("Nette\Security\User");
$user->guestRole = $groupModel->get(?)->singleName;
$user->authenticatedRole = $groupModel->get(?)->singleName;
\Nella\Forms\DateTime\DateInput::register();
\Nella\Forms\DateTime\DateTimeInput::register();', [Nexendrie\Model\Group::class, $roles["guestRole"], $roles["loggedInRole"]]);
  }
}
?>