<?php
/**
 * Craft Cookie consent plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

namespace dutchheight\craftcookieconsent\variables;

use dutchheight\craftcookieconsent\CraftCookieConsent;
use dutchheight\craftcookieconsent\services\ConsentTypeService;

use Craft;
use craft\web\View;
use craft\elements\Entry;

/**
 * Craft Cookie consent Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftCookieConsent }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Dutch Height
 * @package   CraftCookieConsent
 * @since     1.0.0
 */
class CraftCookieConsentVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.craftCookieConsent.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.craftCookieConsent.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function askConsent($displayIfCookiesSet = false, $templateSettings = null)
    {
        $view = Craft::$app->getView();
        if ($this->hasConsentCookie() && !$displayIfCookiesSet) {
            return;
        }

        if (is_null($templateSettings)) {
            $templateSettings = $this->getDefaultTemplateSettings();
        }

        $settings = CraftCookieConsent::$settings;
        $settings['cookiesPageId'] = Entry::findOne(['id' => $settings['cookiesPageId']]);

        $view->setTemplateMode(View::TEMPLATE_MODE_CP);
        echo $view->renderTemplate('craft-cookie-consent/askConsent/_index', [
            'templateSettings'  => $templateSettings,
            'settings'          => $settings,
            'consentTypes'      => ConsentTypeService::getAllEnabled()
        ]);
        $view->setTemplateMode(View::TEMPLATE_MODE_SITE);
    }
    
    /**
     *
     * @param string $handle
     * @return boolean
     */
    public function isConsentWith($handle)
    {
        if (!$this->hasConsentCookie()) {
            return false;
        }

        $cookies = $this->getConsentCookies();
        $settings = json_decode($cookies->value, true);
        if (!key_exists($handle, $settings)) {
            return false;
        }
        return $settings[$handle];
    }
    
    /**
     *
     * @return Array
     */
    public function getConsents()
    {
        if (!$this->hasConsentCookie()) {
            return [];
        }

        $cookies = $this->getConsentCookies();
        return json_decode($cookies->value, true);
    }

    /** 
     * @param null $optional
     * @return string
     */
    public function getConsentsRaw()
    {
        return ConsentTypeService::getAllEnabled();
    }

    // Private Methods
    // =========================================================================

    /**
     *
     * @return Boolean
     */
    private function hasConsentCookie() {
        return Craft::$app->getRequest()->getCookies()->has('craft-cookie-consent');
    }

    /**
     *
     * @return Array
     */
    private function getConsentCookies() {
        return Craft::$app->getRequest()->getCookies()->get('craft-cookie-consent');
    }

    /**
     *
     * @return Array
     */
    private function getDefaultTemplateSettings() {
        return [
            'position' => 'bottom-right'
        ];
    }
}
