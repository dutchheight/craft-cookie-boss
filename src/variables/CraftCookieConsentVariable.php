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
use dutchheight\craftcookieconsent\services\ConsentGroupService;
use dutchheight\craftcookieconsent\services\CookieDescriptionService;

use Craft;
use craft\web\View;
use craft\elements\Entry;
use dutchheight\craftcookieconsent\services\ConsentService;

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
     * @param null $optional
     * @return string
     */
    public function askConsent($templateSettings = null, $displayIfCookiesSet = false)
    {
        $view = Craft::$app->getView();
        
        if (ConsentService::hasConsentCookie() && !$displayIfCookiesSet) {
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
            'consentGroups'      => ConsentGroupService::getAllEnabled()
        ]);
        $view->setTemplateMode(View::TEMPLATE_MODE_SITE);
    }
    
    /**
     *
     * @param string $handle
     * @return boolean
     */
    public function isConsentWith($handle, $concentIfNotSet = false)
    {
        return ConsentService::isConsentWith($handle, $concentIfNotSet);
    }
    
    /**
     *
     * @return JSON
     */
    public function getConsents($defaultConcentIfNotSet = false)
    {
        if (!ConsentService::hasConsentCookie()) {
            if ($defaultConcentIfNotSet) {
                $consents = [];
                foreach (ConsentGroupService::getAllEnabled() as $consent) {
                    $consents[$consent['handle']] = (bool)$consent['defaultValue'];
                }
                return $consents;
            }
            return [];
        }

        $cookies = ConsentService::getConsentCookies();
        return json_decode($cookies->value, true);
    }

    /**
     * @param null $optional
     * @return string
     */
    public function getCookies()
    {
        $view = Craft::$app->getView();
        $view->setTemplateMode(View::TEMPLATE_MODE_CP);
        echo $view->renderTemplate('craft-cookie-consent/cookieDescription/_index', [
            'cookieDescriptions' => CookieDescriptionService::getAllEnabled()
        ]);
        $view->setTemplateMode(View::TEMPLATE_MODE_SITE);
    }

    /** 
     * @param null $optional
     * @return string
     */
    public function getConsentsRaw()
    {
        return ConsentGroupService::getAllEnabled();
    }
    
    /** 
     * @param null $optional
     * @return string
     */
    public function getCookiesRaw($consentGroupHandle = null)
    {
        return CookieDescriptionService::getAll($consentGroupHandle);
    }

    // Private Methods
    // =========================================================================

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
