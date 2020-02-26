<?php

namespace dutchheight\cookieboss\services;

use Craft;
use craft\base\Component;
use dutchheight\cookieboss\CookieBoss;

use yii\web\Cookie;

class ConsentService extends Component {

    /**
     *
     * @return Boolean
     */
    public function isConsentWith($handle, $concentIfNotSet = false) {
        if (!$this->hasConsentCookie()) {
            return false;
        }

        $cookies = $this->getConsentCookies();
        $settings = json_decode($cookies->value, true);
        if (!key_exists($handle, $settings)) {
            return $concentIfNotSet;
        }
        return $settings[$handle];
    }

    /**
     * Check for valid consent cookies and consent given at time
     * @return Boolean
     */
    public function hasConsentCookie() {

        if (!Craft::$app->getRequest()->getCookies()->has('cookie-boss') || !Craft::$app->getRequest()->getCookies()->has('cookie-boss-consent-at')) {
            return false;
        }

        if ((int)CookieBoss::$settings->lastSettingsUpdate > (int)Craft::$app->getRequest()->getCookies()->get('cookie-boss-consent-at')->value) {
            return false;
        }

        return true;
    }

    /**
     * Check for valid consent cookies and consent given at time
     * @return int|null
     */
    public function getConsentGivenAt() {

        if (Craft::$app->getRequest()->getCookies()->has('cookie-boss-consent-at')) {
            return (int)Craft::$app->getRequest()->getCookies()->get('cookie-boss-consent-at')->value;
        }

        return null;
    }

    /**
     *
     * @return Array
     */
    public function getConsentCookies() {
        return Craft::$app->getRequest()->getCookies()->get('cookie-boss');
    }

    public function generateCookieData($groups) {
        $originalData = CookieBoss::getInstance()->consentGroups->getAllEnabled();
        $currentCookieBoss = [];

        foreach ($originalData as $consentsGroups) {
            $handle = $consentsGroups->handle;
            $allowed = false;

            if (key_exists($handle, $groups)) {
                $allowed = $groups[$handle];
            } else {
                $allowed = $consentsGroups->defaultValue;
            }

            if ($consentsGroups->required) {
                $allowed = true;
            }
            $currentCookieBoss[$handle] = boolval($allowed);
        }

        return $currentCookieBoss;
    }

    /**
     * @var Array ['handle' => 'boolean']
     * @return Void
     */
    public function saveConsentCookies($data) {
        $cookies = Craft::$app->response->cookies;
        $cookies->remove('cookie-boss');
        $cookies->remove('cookie-boss-consent-at');

        $cookies->add(new Cookie([
            'name' => 'cookie-boss',
            'value' => json_encode($data),
            'expire' => time() + CookieBoss::$settings->cookieTime
        ]));

        // Set current time
        $cookies->add(new Cookie([
            'name' => 'cookie-boss-consent-at',
            'value' => json_encode(time()),
            'expire' => time() + CookieBoss::$settings->cookieTime
        ]));
    }
}