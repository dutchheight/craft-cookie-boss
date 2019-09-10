<?php
/**
 * Craft Cookie consent plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

namespace dutchheight\craftcookieconsent\models;

use dutchheight\craftcookieconsent\CraftCookieConsent;
use dutchheight\craftcookieconsent\services\ConsentTypeService;

use Craft;
use craft\base\Model;


/**
 * CraftCookieConsent Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Dutch Height
 * @package   CraftCookieConsent
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
    * Is the plugin enabled
    *
    * @var boolean
    */
    public $cookieTime = 604800;

    /**
    * Is the plugin enabled
    *
    * @var boolean
    */
    public $enabled = true;

    /**
    * If false cookies are allowed when using the website
    *
    * @var boolean
    */
    public $presentTypes = false;

    /**
    * If false cookies are allowed when using the website
    *
    * @var boolean
    */
    public $forceAccept = false;

    /**
    * The modal title
    *
    * @var string
    */
    public $title = "Cookies... Again!";

    /**
    * The modal message
    *
    * @var string
    */
    public $message = "Cookie message.";

    /**
    * The settings modal message
    *
    * @var string
    */
    public $messageSettings = "Cookie settings message.";

    /**
    * The settings button text
    *
    * @var string
    */
    public $settingsButtonText = "Settings";

    /**
    * The accept button text
    *
    * @var string
    */
    public $acceptButtonText = "Accept";

    /**
    * The accept button text
    *
    * @var number
    */
    public $cookiesPageId = null;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['enabled', 'boolean'],
            [['title', 'message', 'settingsButtonText', 'acceptButtonText', 'enabled'], 'required'],
        ];
    }

    /**
     * Returns all consentTypes.
     *
     * @return array
     */
    public function consentTypes() {
        return ConsentTypeService::getAll();
    }
}
