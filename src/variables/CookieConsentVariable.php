<?php
/**
 * Craft Cookie boss plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

namespace dutchheight\cookieboss\variables;

use dutchheight\cookieboss\CookieBoss;
use dutchheight\cookieboss\services\ConsentGroupService;
use dutchheight\cookieboss\services\CookieDescriptionService;
use dutchheight\cookieboss\services\ConsentService;

use Craft;
use craft\web\View;
use craft\elements\Entry;

/**
 * Cookie consent Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.CookieBoss }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Dutch Height
 * @package   CookieBoss
 * @since     1.0.0
 */
class CookieBossVariable
{
    // Public Methods
    // =========================================================================

    //
    // ─── HTML ───────────────────────────────────────────────────────────────────────────
    //

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

        $settings = CookieBoss::$settings;
        $settings['cookiesPageId'] = Entry::findOne(['id' => $settings['cookiesPageId']]);

        $view->setTemplateMode(View::TEMPLATE_MODE_CP);
        echo $view->renderTemplate('cookie-boss/askConsent/_index', [
            'templateSettings'  => $templateSettings,
            'settings'          => $settings,
            'consentGroups'      => ConsentGroupService::getAllEnabled()
        ]);
        $view->setTemplateMode(View::TEMPLATE_MODE_SITE);
    }

    /**
     * @param null $optional
     * @return string
     */
    public function toggleConsentGroupForm($consentGroupHandle, $options = null)
    {
        $view = Craft::$app->getView();
        $view->setTemplateMode(View::TEMPLATE_MODE_CP);
        echo $view->renderTemplate('cookie-boss/toggleConsentGroup/_index', [
            'handle'            => $consentGroupHandle,
            'consentGroup'      => ConsentGroupService::getAllByHandle($consentGroupHandle),
            'options'           => $options
        ]);
        $view->setTemplateMode(View::TEMPLATE_MODE_SITE);
    }

    /**
     * @return string
     */
    public function getCookies()
    {
        $view = Craft::$app->getView();
        $view->setTemplateMode(View::TEMPLATE_MODE_CP);
        echo $view->renderTemplate('cookie-boss/cookieDescription/_index', [
            'cookieDescriptions' => CookieDescriptionService::getAllEnabled()
        ]);
        $view->setTemplateMode(View::TEMPLATE_MODE_SITE);
    }

    //
    // ─── Checks ───────────────────────────────────────────────────────────────────────────
    //

    /**
     *
     * @param string $handle
     * @param boolean $concentIfNotSet
     * @return boolean
     */
    public function isConsentWith($handle, $concentIfNotSet = false)
    {
        return ConsentService::isConsentWith($handle, $concentIfNotSet);
    }

    /**
     * @var boolean $defaultConcentIfNotSet return default if consent not set
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

    //
    // ─── Consent groups ───────────────────────────────────────────────────────────────────────────
    //

    /**
     * @param boolean $onlyEnabled only show enabled consent groups
     * @return Array<CookieDescription>
     */
    public function getConsentsGroupsRaw($onlyEnabled = false)
    {
        if ($onlyEnabled) {
            return ConsentGroupService::getAllEnabled();
        }

        return ConsentGroupService::getAll();
    }

    /**
     * @param string $consentGroupHandle
     * @return string
     */
    public function getConsentsGroupRawByHandle($consentGroupHandle)
    {
        return ConsentGroupService::getAllByHandle($consentGroupHandle);
    }

    //
    // ─── Cookies ───────────────────────────────────────────────────────────────────────────
    //

    /**
     * @param string $consentGroupHandle
     * @return CookieDescription
     */
    public function getCookiesRaw($consentGroupHandle = null)
    {
        return CookieDescriptionService::getAll($consentGroupHandle);
    }

    // Private Methods
    // =========================================================================

    private function getDefaultTemplateSettings() {
        return [
            'position' => 'bottom-right'
        ];
    }
}
