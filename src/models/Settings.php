<?php
/**
 * Craft Cookie boss plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

namespace dutchheight\cookieboss\models;

use craft\base\Model;

/**
 * CookieBoss Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Dutch Height
 * @package   CookieBoss
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
    * Is the plugin enabled
    *
    * @var number
    */
    public $cookieTime = 604800;

    /**
    * Is the plugin enabled
    *
    * @var boolean
    */
    public $enabled = true;

    /**
    * If true the default modal presents all cookie groups
    *
    * @var boolean
    */
    public $presentGroups = true;

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
    * The accept button text of the settings
    *
    * @var string
    */
    public $acceptButtonSettingsText = "Accept";

    /**
    * The cookie detailpage id
    *
    * @var number
    */
    public $cookiesPageId = null;

    /**
    * Accept after amount of time
    *
    * @var number
    */
    public $acceptAfterSeconds = 0;

    /**
    * Last cookie settings update (Unix timestamp)
    *
    * @var int
    */
    public $lastSettingsUpdate = null;

    /**
    * The position (css class) of the modal
    *
    * @var string
    */
    public $position = "bottom-right";

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
     * Returns all consentGroups.
     *
     * @return array
     */
    public function consentGroups() {
        return CookieBoss::getInstance()->consentGroups->getAll();
    }

    /**
     * Returns all cookie descriptions.
     *
     * @return array
     */
    public function cookieDescriptions() {
        return CookieBoss::getInstance()->cookieDescriptions->getAll();
    }
}
