<?php
/**
 * Craft Cookie consent plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

namespace dutchheight\craftcookieconsent;

use dutchheight\craftcookieconsent\variables\CraftCookieConsentVariable;
use dutchheight\craftcookieconsent\twigextensions\CraftCookieConsentTwigExtension;
use dutchheight\craftcookieconsent\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;


/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Dutch Height
 * @package   CraftCookieConsent
 * @since     1.0.0
 *
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftCookieConsent extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftCookieConsent::$plugin
     *
     * @var CraftCookieConsent
     */
    public static $plugin;

    /**
     * @var Settings
     */
    public static $settings;


    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftCookieConsent::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
        self::$settings = $this->getSettings();

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftCookieConsent', CraftCookieConsentVariable::class);
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craft-cookie-consent/save-consent-settings'] = 'craft-cookie-consent/consent/save-consent-settings';
            }
        );

        // Install only for non-console Control Panel requests
        $request = Craft::$app->getRequest();
        if ($request->getIsCpRequest() && !$request->getIsConsoleRequest()) {
            $this->registerCpEventListeners();
        }
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('craft-cookie-consent/settings'));
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function registerCpEventListeners()
    {
        // Handler: UrlManager::EVENT_REGISTER_CP_URL_RULES
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge(
                    $event->rules,
                    ['craft-cookie-consent/settings' => 'craft-cookie-consent/settings/plugin-settings']
                );
            }
        );
    }

}
