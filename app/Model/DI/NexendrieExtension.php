<?php
declare(strict_types=1);

namespace Nexendrie\Model\DI;

use Nette\DI\Definitions\FactoryDefinition;
use Nexendrie;
use Nexendrie\Model\SettingsRepository;
use Nette\Bridges\ApplicationLatte\ILatteFactory;

/**
 * Nexendrie Extension for DIC
 *
 * @author Jakub Konečný
 */
final class NexendrieExtension extends \Nette\DI\CompilerExtension {
  public function __construct(private readonly string $wwwDir, private readonly string $appDir) {
  }

  public function loadConfiguration(): void {
    $this->registerMenuConditions();
    $this->addChatCommands();
    $this->addCombat();
    $this->addModels();
    $this->addCronTasks();
    $this->addComponents();
    $this->addForms();
    $this->addAchievements();
  }

  private function registerMenuConditions(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition("menu.condition.banned")
      ->setType(Nexendrie\Menu\ConditionBanned::class);
    $builder->addDefinition("menu.condition.path")
      ->setType(Nexendrie\Menu\ConditionPath::class);
  }

  private function addChatCommands(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition("chat.command.time")
      ->setType(Nexendrie\Chat\Commands\TimeCommand::class);
  }

  private function addCombat(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("combat.combatHelper"))
      ->setType(Nexendrie\Model\CombatHelper::class);
    $builder->addDefinition($this->prefix("combat.combat"))
      ->setType(\HeroesofAbenez\Combat\CombatBase::class);
    $builder->addDefinition($this->prefix("combat.logger"))
      ->setType(\HeroesofAbenez\Combat\CombatLogger::class);
    $builder->addDefinition($this->prefix("combat.logRender"))
      ->setType(\HeroesofAbenez\Combat\TextCombatLogRender::class);
    $builder->addDefinition($this->prefix("combat.successCalculator"))
      ->setType(\HeroesofAbenez\Combat\StaticSuccessCalculator::class);
    $builder->addDefinition($this->prefix("combat.actionSelector"))
      ->setType(\HeroesofAbenez\Combat\CombatActionSelector::class);
  }

  private function addModels(): void {
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
    $builder->addDefinition($this->prefix("model.achievements"))
      ->setType(Nexendrie\Model\Achievements::class);
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
    $builder->addDefinition($this->prefix("model.openSearch"))
      ->setType(Nexendrie\Model\OpenSearch::class);
    $builder->addDefinition($this->prefix("model.moderation"))
      ->setType(Nexendrie\Model\Moderation::class);
    $builder->addDefinition($this->prefix("model.themesManager"))
      ->setFactory(Nexendrie\Model\ThemesManager::class, [$this->wwwDir]);
    $builder->addDefinition($this->prefix("model.genericNotificator"))
      ->setType(Nexendrie\Model\GenericNotificator::class);
    $builder->addDefinition($this->prefix("model.workNotificator"))
      ->setType(Nexendrie\Model\WorkNotificator::class);
  }

  private function addCronTasks(): void {
    $builder = $this->getContainerBuilder();
    $tag = \stekycz\Cronner\DI\CronnerExtension::TASKS_TAG;
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

  private function addComponents(): void {
    $builder = $this->getContainerBuilder();
    $builder->addFactoryDefinition($this->prefix("component.poll"))
      ->setImplement(Nexendrie\Components\IPollControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.shop"))
      ->setImplement(Nexendrie\Components\IShopControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.mountsMarket"))
      ->setImplement(Nexendrie\Components\IMountsMarketControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.academy"))
      ->setImplement(Nexendrie\Components\IAcademyControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.townsMarket"))
      ->setImplement(Nexendrie\Components\ITownsMarketControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.help"))
      ->setImplement(Nexendrie\Components\IHelpControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.stables"))
      ->setImplement(Nexendrie\Components\IStablesControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.prison"))
      ->setImplement(Nexendrie\Components\IPrisonControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.tavern"))
      ->setImplement(Nexendrie\Components\ITavernControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.adventure"))
      ->setImplement(Nexendrie\Components\IAdventureControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.history"))
      ->setImplement(Nexendrie\Components\IHistoryControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.wedding"))
      ->setImplement(Nexendrie\Components\IWeddingControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.elections"))
      ->setImplement(Nexendrie\Components\IElectionsControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.userProfileLink"))
      ->setImplement(Nexendrie\Components\IUserProfileLinkControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.favicon"))
      ->setImplement(Nexendrie\Components\IFaviconControlFactory::class);
    $builder->addFactoryDefinition($this->prefix("component.sharer"))
      ->setImplement(Nexendrie\Components\ISharerControlFactory::class);
    $builder->addDefinition($this->prefix("component.sharer.link.facebook"))
      ->setType(Nexendrie\Components\SharerLinks\Facebook::class);
    $builder->addDefinition($this->prefix("component.sharer.link.twitter"))
      ->setType(Nexendrie\Components\SharerLinks\Twitter::class);
    $builder->addDefinition($this->prefix("component.sharer.link.fediverse"))
      ->setType(Nexendrie\Components\SharerLinks\Fediverse::class);
    $builder->addFactoryDefinition("component.socialIcons")
      ->setImplement(Nexendrie\Components\ISocialIconsControlFactory::class);
    $builder->addDefinition($this->prefix("component.socialIcons.icon.facebook"))
      ->setType(Nexendrie\Components\SocialIcons\Facebook::class);
    $builder->addDefinition($this->prefix("component.socialIcons.icon.twitter"))
      ->setType(Nexendrie\Components\SocialIcons\Twitter::class);
    $builder->addDefinition($this->prefix("component.socialIcons.icon.friendica"))
      ->setType(Nexendrie\Components\SocialIcons\Friendica::class);
    $builder->addDefinition($this->prefix("component.socialIcons.icon.gitlab"))
      ->setType(Nexendrie\Components\SocialIcons\GitLab::class);
  }

  private function addForms(): void {
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
    $builder->addDefinition($this->prefix("form.openDepositAccount"))
      ->setType(Nexendrie\Forms\OpenDepositAccountFormFactory::class);
    $builder->addDefinition($this->prefix("form.siteSearch"))
      ->setType(Nexendrie\Forms\SiteSearchFormFactory::class);
  }

  private function addAchievements(): void {
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("achievements.completedAdventures"))
      ->setType(Nexendrie\Achievements\CompletedAdventuresAchievement::class);
    $builder->addDefinition($this->prefix("achievements.completedJobs"))
      ->setType(Nexendrie\Achievements\CompletedJobsAchievement::class);
    $builder->addDefinition($this->prefix("achievements.lessonsTaken"))
      ->setType(Nexendrie\Achievements\LessonsTakenAchievement::class);
    $builder->addDefinition($this->prefix("achievements.producedBeers"))
      ->setType(Nexendrie\Achievements\ProducedBeersAchievement::class);
    $builder->addDefinition($this->prefix("achievements.townsOwned"))
      ->setType(Nexendrie\Achievements\TownsOwnedAchievements::class);
    $builder->addDefinition($this->prefix("achievements.mountsOwned"))
      ->setType(Nexendrie\Achievements\MountsOwnedAchievement::class);
    $builder->addDefinition($this->prefix("achievements.writtenArticles"))
      ->setType(Nexendrie\Achievements\WrittenArticlesAchievement::class);
  }

  public function beforeCompile(): void {
    $builder = $this->getContainerBuilder();
    /** @var FactoryDefinition $latteFactory */
    $latteFactory = $builder->getDefinitionByType(ILatteFactory::class);
    $latteFactory->getResultDefinition()->addSetup("addFilter", ["genderify", ["@" . $this->prefix("model.locale"), "genderMessage"]]);
    $latteFactory->getResultDefinition()->addSetup("addFilter", ["money", ["@" . $this->prefix("model.locale"), "money"]]);
  }

  public function afterCompile(\Nette\PhpGenerator\ClassType $class): void {
    $this->initialization->addBody('$roles = $this->getByType(?)->settings["roles"];
$groupModel = $this->getByType(?);
$user = $this->getByType(?);
$user->guestRole = $groupModel->get($roles["guestRole"])->singleName;
$user->authenticatedRole = $groupModel->get($roles["loggedInRole"])->singleName;', [SettingsRepository::class, Nexendrie\Model\Group::class, \Nette\Security\User::class]);
  }
}
?>