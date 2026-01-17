<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\LinkGenerator;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Nexendrie\Model\ThemesManager;
use Nexendrie\Model\UserManager;
use Nexendrie\Model\SettingsException;
use Nexendrie\Orm\User as UserEntity;

/**
 * Factory for form UserSettings
 *
 * @author Jakub Konečný
 */
final class UserSettingsFormFactory
{
    public function __construct(
        private readonly UserManager $model,
        private readonly ThemesManager $themesManager,
        private readonly LinkGenerator $linkGenerator
    ) {
    }

    public function create(): Form
    {
        $defaultValues = $this->model->getSettings();
        $form = new Form();
        $form->addGroup("Účet");
        $form->addText("publicname", "Zobrazované jméno:")
            ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25)
            ->setRequired("Zadej jméno.");
        $form->addEmail("email", "E-mail:")
            ->setRequired("Zadej e-mail.");
        $form->addRadioList("gender", "Pohlaví:", UserEntity::getGenders())
            ->setRequired("Vyber pohlaví.");
        $form->addSelect("style", "Vzhled stránek:", $this->themesManager->getList());
        $form->addGroup("Heslo")
            ->setOption("description", "Současné a nové heslo vyplňujte jen pokud ho chcete změnit.");
        $passwordOld = $form->addPassword("password_old", "Současné heslo:");
        $passwordNew = $form->addPassword("password_new", "Nové heslo:");
        $passwordOld->addConditionOn($passwordNew, Form::FILLED)
            ->setRequired("Musíš zadat současné heslo.");
        $form->addPassword("password_check", "Nové heslo (kontrola):")
            ->addConditionOn($passwordNew, Form::FILLED)
            ->setRequired("Musíš znovu zadat nové heslo.")
            ->addRule(Form::EQUAL, "Hesla se neshodují.", $form["password_new"]);
        $form->addGroup("Upozorňování")
            ->setOption("description", "Zapne upozorňování např. na nepřečtené zprávy. Nejdříve je třeba je povolit na účtu a po uložení změn zapnout v prohlížeči. Upozornění se zobrazí jen pokud máte otevřené tyto stránky.");
        $form->addCheckbox("notifications", "Povolit upozorňování na tomto účtu");
        if ($defaultValues['notifications']) {
            $form->addButton('notifications_browser', "Zapnout v prohlížeči")
                ->setHtmlId("notifications_browser")
                ->setHtmlAttribute("onclick", "notificationsSetup()");
        }
        $apiTokensLink = $this->linkGenerator->link("Front:User:apiTokens");
        $form->addGroup("API")
            ->setOption("description", Html::el("p")->setHtml("Povolí používání API pro tento účet. Svoje platné tokeny můžeš spravovat <a href=\"$apiTokensLink\">zde</a>."));
        $form->addCheckbox("api", "Zapnout API pro tento účet");
        $form->setCurrentGroup(null);
        $form->addSubmit("save", "Uložit změny");
        $form->setDefaults($defaultValues);
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->model->changeSettings($values);
        } catch (SettingsException $e) {
            if ($e->getCode() === UserManager::REG_DUPLICATE_NAME) {
                $form->addError("Zvolené jméno je už zabráno.");
            }
            if ($e->getCode() === UserManager::REG_DUPLICATE_EMAIL) {
                $form->addError("Zadaný e-mail je už používán.");
            }
            if ($e->getCode() === UserManager::SET_INVALID_PASSWORD) {
                $form->addError("Neplatné heslo.");
            }
        }
    }
}
