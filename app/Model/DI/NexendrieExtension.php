<?php
declare(strict_types=1);

namespace Nexendrie\Model\DI;

use Nexendrie;
use Nexendrie\Model\SettingsRepository;

/**
 * Nexendrie Extension for DIC
 *
 * @author Jakub Konečný
 */
class NexendrieExtension extends \Nette\DI\CompilerExtension {
  /** @var string */
  protected $appDir;
  
  public function __construct(string $appDir) {
    $this->appDir = $appDir;
  }
  
  public function loadConfiguration(): void {
    $this->registerMenuConditions();
    $this->addModels();
    $this->addCronTasks();
    $this->addComponents();
    $this->addForms();
  }
  
  protected function registerMenuConditions(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition("menu.condition.banned")
      ->setType(Nexendrie\Menu\ConditionBanned::class);
    $builder->addDefinition("menu.condition.path")
      ->setType(Nexendrie\Menu\ConditionPath::class);
  }
  
  protected function addModels(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig();
    $builder->addDefinition($this->prefix("model.group"))
      ->setType(Nexendrie\Model\Group::class);
    $builder->addDefinition($this->prefix("model.market"))
      ->setType(Nexendrie\Model\Market::class);
    $builder->addDefinition($this->prefix("model.messenger"))
      ->setType(Nexendrie\Model\Messenger::class);
    $builder->addDefinition($this->prefix("model.polls"))
      ->setType(Nexendrie\Model\Polls::class);
    $builder->addDefinition($this->prefix("model.profile"))
      ->setType(Nexendrie\Model\Profile::class);
    $builder->addDefinition($this->prefix("model.rss"))
      ->setType(Nexendrie\Model\Rss::class);
    $builder->addDefinition($this->prefix("model.property"))
      ->setType(Nexendrie\Model\Property::class);
    $builder->addDefinition($this->prefix("model.job"))
      ->setType(Nexendrie\Model\Job::class);
    $builder->addDefinition($this->prefix("model.town"))
      ->setType(Nexendrie\Model\Town::class);
    $builder->addDefinition($this->prefix("model.mount"))
      ->setType(Nexendrie\Model\Mount::class);
    $builder->addDefinition($this->prefix("model.skills"))
      ->setType(Nexendrie\Model\Skills::class);
    $builder->addDefinition($this->prefix("model.chronicle"))
      ->setType(Nexendrie\Model\Chronicle::class);
    $builder->addDefinition($this->prefix("model.tavern"))
      ->setType(Nexendrie\Model\Tavern::class);
    $builder->addDefinition($this->prefix("model.inventory"))
      ->setType(Nexendrie\Model\Inventory::class);
    $builder->addDefinition($this->prefix("model.adventure"))
      ->setType(Nexendrie\Model\Adventure::class);
    $builder->addDefinition($this->prefix("model.combat"))
      ->setType(Nexendrie\Model\Combat::class);
    $builder->addDefinition($this->prefix("model.events"))
      ->setType(Nexendrie\Model\Events::class);
    $builder->addDefinition($this->prefix("model.house"))
      ->setType(Nexendrie\Model\House::class);
    $builder->addDefinition($this->prefix("model.itemSet"))
      ->setType(Nexendrie\Model\ItemSet::class);
    $builder->addDefinition($this->prefix("model.marriage"))
      ->setType(Nexendrie\Model\Marriage::class);
    $builder->addDefinition($this->prefix("model.elections"))
      ->setType(Nexendrie\Model\Elections::class);
    $builder->addDefinition($this->prefix("model.article"))
      ->setType(Nexendrie\Model\Article::class);
    $builder->addDefinition($this->prefix("model.userManager"))
      ->setType(Nexendrie\Model\UserManager::class);
    $builder->addDefinition($this->prefix("model.authenticator"))
      ->setType(Nexendrie\Model\Authenticator::class);
    $builder->addDefinition($this->prefix("model.locale"))
      ->setType(Nexendrie\Model\Locale::class);
    $builder->addDefinition($this->prefix("model.bank"))
       ->setType(Nexendrie\Model\Bank::class);
    $builder->addDefinition($this->prefix("model.taxes"))
        ->setType(Nexendrie\Model\Taxes::class);
    $builder->addDefinition($this->prefix("model.monastery"))
      ->setType(Nexendrie\Model\Monastery::class);
    $builder->addDefinition($this->prefix("model.castle"))
      ->setType(Nexendrie\Model\Castle::class);
    $builder->addDefinition($this->prefix("model.guild"))
      ->setType(Nexendrie\Model\Guild::class);
    $builder->addDefinition($this->prefix("model.order"))
      ->setType(Nexendrie\Model\Order::class);
    $builder->addDefinition("cache.cache")
      ->setFactory(\Nette\Caching\Cache::class, ["@cache.storage", "data"]);
    $builder->addDefinition($this->prefix("model.settingsRepository"))
      ->setFactory(Nexendrie\Model\SettingsRepository::class, [$config]);
    $builder->addDefinition($this->prefix("model.authorizatorFactory"))
      ->setType(Nexendrie\Model\AuthorizatorFactory::class);
    $builder->addDefinition($this->prefix("model.authorizator"))
      ->setType(\Nette\Security\Permission::class)
      ->setFactory("@" . Nexendrie\Model\AuthorizatorFactory::class . "::create");
    $builder->removeDefinition("router");
    $builder->addDefinition($this->prefix("model.routerFactory"))
      ->setType(Nexendrie\Model\RouterFactory::class);
    $builder->addDefinition($this->prefix("model.router"))
      ->setType(\Nette\Application\Routers\RouteList::class)
      ->setFactory("@" . Nexendrie\Model\RouterFactory::class . "::create");
  }
  
  protected function addCronTasks(): void {
    $builder = $this->getContainerBuilder();
    $tag = "cronner.tasks";
    $builder->addDefinition($this->prefix("cron.mountsStatus"))
      ->setType(Nexendrie\Cron\MountsStatusTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.taxes"))
      ->setType(Nexendrie\Cron\TaxesTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.guildFees"))
      ->setType(Nexendrie\Cron\GuildFeesTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.orderFees"))
      ->setType(Nexendrie\Cron\OrderFeesTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.closeAdventures"))
      ->setType(Nexendrie\Cron\CloseAdventuresTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.monasteriesStatus"))
      ->setType(Nexendrie\Cron\MonasteriesStatusTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.castlesStatus"))
      ->setType(Nexendrie\Cron\CastlesStatusTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.housesStatus"))
      ->setType(Nexendrie\Cron\HousesStatusTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.closeWeddings"))
      ->setType(Nexendrie\Cron\CloseWeddingsTask::class)
      ->addTag($tag);
    $builder->addDefinition($this->prefix("cron.municipalElections"))
      ->setType(Nexendrie\Cron\MunicipalElectionsTask::class)
      ->addTag($tag);
  }
  
  protected function addComponents(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("component.poll"))
      ->setImplement(Nexendrie\Components\IPollControlFactory::class);
    $builder->addDefinition($this->prefix("component.shop"))
      ->setImplement(Nexendrie\Components\IShopControlFactory::class);
    $builder->addDefinition($this->prefix("component.mountsMarket"))
      ->setImplement(Nexendrie\Components\IMountsMarketControlFactory::class);
    $builder->addDefinition($this->prefix("component.academy"))
      ->setImplement(Nexendrie\Components\IAcademyControlFactory::class);
    $builder->addDefinition($this->prefix("component.townsMarket"))
      ->setImplement(Nexendrie\Components\ITownsMarketControlFactory::class);
    $builder->addDefinition($this->prefix("component.help"))
      ->setImplement(Nexendrie\Components\IHelpControlFactory::class);
    $builder->addDefinition($this->prefix("component.stables"))
      ->setImplement(Nexendrie\Components\IStablesControlFactory::class);
    $builder->addDefinition($this->prefix("component.prison"))
      ->setImplement(Nexendrie\Components\IPrisonControlFactory::class);
    $builder->addDefinition($this->prefix("component.tavern"))
      ->setImplement(Nexendrie\Components\ITavernControlFactory::class);
    $builder->addDefinition($this->prefix("component.adventure"))
      ->setImplement(Nexendrie\Components\IAdventureControlFactory::class);
    $builder->addDefinition($this->prefix("component.history"))
      ->setImplement(Nexendrie\Components\IHistoryControlFactory::class);
    $builder->addDefinition($this->prefix("component.wedding"))
      ->setImplement(Nexendrie\Components\IWeddingControlFactory::class);
    $builder->addDefinition($this->prefix("component.elections"))
      ->setImplement(Nexendrie\Components\IElectionsControlFactory::class);
  }
  
  protected function addForms(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("form.addEditArticle"))
      ->setType(Nexendrie\Forms\AddEditArticleFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditPoll"))
      ->setType(Nexendrie\Forms\AddEditPollFormFactory::class);
    $builder->addDefinition($this->prefix("form.newMessage"))
      ->setType(Nexendrie\Forms\NewMessageFormFactory::class);
    $builder->addDefinition($this->prefix("form.register"))
      ->setType(Nexendrie\Forms\RegisterFormFactory::class);
    $builder->addDefinition($this->prefix("form.login"))
      ->setType(Nexendrie\Forms\LoginFormFactory::class);
    $builder->addDefinition($this->prefix("form.userSettings"))
      ->setType(Nexendrie\Forms\UserSettingsFormFactory::class);
    $builder->addDefinition($this->prefix("form.addComment"))
      ->setType(Nexendrie\Forms\AddCommentFormFactory::class);
    $builder->addDefinition($this->prefix("form.editGroup"))
      ->setType(Nexendrie\Forms\EditGroupFormFactory::class);
    $builder->addDefinition($this->prefix("form.systemSettings"))
      ->setFactory(Nexendrie\Forms\SystemSettingsFormFactory::class, [$this->appDir]);
    $builder->addDefinition($this->prefix("form.editUser"))
      ->setType(Nexendrie\Forms\EditUserFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditShop"))
      ->setType(Nexendrie\Forms\AddEditShopFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditItem"))
      ->setType(Nexendrie\Forms\AddEditItemFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditJob"))
      ->setType(Nexendrie\Forms\AddEditJobFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditJobMessage"))
      ->setType(Nexendrie\Forms\AddEditJobMessageFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditTown"))
      ->setType(Nexendrie\Forms\AddEditTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditMount"))
      ->setType(Nexendrie\Forms\AddEditMountFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditSkill"))
      ->setType(Nexendrie\Forms\AddEditSkillFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageMount"))
      ->setType(Nexendrie\Forms\ManageMountFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageTown"))
      ->setType(Nexendrie\Forms\ManageTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.banUser"))
      ->setType(Nexendrie\Forms\BanUserFormFactory::class);
    $builder->addDefinition($this->prefix("form.takeLoan"))
      ->setType(Nexendrie\Forms\TakeLoanFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditMeal"))
      ->setType(Nexendrie\Forms\AddEditMealFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditAdventure"))
      ->setType(Nexendrie\Forms\AddEditAdventureFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditAdventureEnemy"))
      ->setType(Nexendrie\Forms\AddEditAdventureEnemyFormFactory::class);
    $builder->addDefinition($this->prefix("form.buildMonastery"))
      ->setType(Nexendrie\Forms\BuildMonasteryFormFactory::class);
    $builder->addDefinition($this->prefix("form.monasteryDonate"))
      ->setType(Nexendrie\Forms\MonasteryDonateFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageMonastery"))
      ->setType(Nexendrie\Forms\ManageMonasteryFormFactory::class);
    $builder->addDefinition($this->prefix("form.appointMayor"))
      ->setType(Nexendrie\Forms\AppointMayorFormFactory::class);
    $builder->addDefinition($this->prefix("form.buildCastle"))
      ->setType(Nexendrie\Forms\BuildCastleFormFactory::class);
    $builder->addDefinition($this->prefix("form.gift"))
      ->setType(Nexendrie\Forms\GiftFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundTown"))
      ->setType(Nexendrie\Forms\FoundTownFormFactory::class);
    $builder->addDefinition($this->prefix("form.makeCitizen"))
      ->setType(Nexendrie\Forms\MakeCitizenFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditEvent"))
      ->setType(Nexendrie\Forms\AddEditEventFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundGuild"))
      ->setType(Nexendrie\Forms\FoundGuildFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageGuild"))
      ->setType(Nexendrie\Forms\ManageGuildFormFactory::class);
    $builder->addDefinition($this->prefix("form.foundOrder"))
      ->setType(Nexendrie\Forms\FoundOrderFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageOrder"))
      ->setType(Nexendrie\Forms\ManageOrderFormFactory::class);
    $builder->addDefinition($this->prefix("form.addEditItemSet"))
      ->setType(Nexendrie\Forms\AddEditItemSetFormFactory::class);
    $builder->addDefinition($this->prefix("form.manageCastle"))
      ->setType(Nexendrie\Forms\ManageCastleFormFactory::class);
    $builder->addDefinition($this->prefix("form.changeWeddingTerm"))
      ->setType(Nexendrie\Forms\ChangeWeddingTermFormFactory::class);
  }
  
  public function afterCompile(\Nette\PhpGenerator\ClassType $class): void {
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$roles = $this->getByType(?)->settings["roles"];
$groupModel = $this->getByType(?);
$user = $this->getByType(?);
$user->guestRole = $groupModel->get($roles["guestRole"])->singleName;
$user->authenticatedRole = $groupModel->get($roles["loggedInRole"])->singleName;', [SettingsRepository::class, Nexendrie\Model\Group::class, \Nette\Security\User::class]);
  }
}
?>